<?php

// Enqueue Signature Pad library
function enqueue_signature_pad_library() {
    // Register Signature Pad library
    wp_register_script( 'signature-pad', 'https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js', array(), '1.5.3', true );

    // Enqueue Signature Pad library
    wp_enqueue_script( 'signature-pad' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_signature_pad_library' );

// Add custom meta box to the edit order dashboard
function add_signature_data_meta_box() {
    add_meta_box(
        'signature_data_meta_box',
        'Tandatangan Pemohon',
        'display_signature_data_meta_box',
        'shop_order',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'add_signature_data_meta_box' );

// Display signature data in the custom meta box
function display_signature_data_meta_box( $post ) {
    $order = wc_get_order( $post->ID );

    if ( $order ) {
        $signature_data = get_post_meta( $order->get_id(), 'signature_data', true );
        if ( $signature_data ) {
            echo '<img src="' . esc_attr( $signature_data ) . '" style="width: 80%;" alt="Signature">';
			 echo '<p><a class="download-button" href="' . esc_attr( $signature_data ) . '" download="' . esc_attr( 'signature_' . $order->get_id() . '.png' ) . '" style="display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold;">Download Signature</a></p>';
        } else {
            echo '<p>No signature data available.</p>';
        }
    }
}


// Add signature field to WooCommerce checkout
function add_signature_field_to_checkout( $checkout ) {
    echo '<style>
        #signature-field {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 150px;
            background-color: #fff;
			box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.05);
        }
        
        #signature-canvas {
            width: 400px;
            height: 200px;
            border: 1px solid #999;
            background-color: #fff;
        }
        
        #signature-popup {
            display: none;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }
        
        #signature-popup-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 400px;
            height: 300px;
            background-color: #fff;
            padding: 20px;
        }
        
        #signature-popup-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        #reset-signature {
            margin-left: 10px;
        }
        
        #signature-title {
            display: none;
        }
        
        #signature-image {
            width: 20%;
            height: auto;
            margin-top: 10px;
        }
		
		#digital{
		margin-top:50px;
		font-size: 25px;
        font-family: poppins;
		font-weight: 500;
		color: black;
		}
		
    </style>';
	
	echo '<div>';
	echo '<h3 id="digital">Tandatangan Pemohon</h3>';
	echo '</div>';
    echo '<div id="signature-field">';
    echo '<input type="text" id="signature-title" name="signature_title" placeholder="' . __( 'Digital Signature', 'woocommerce' ) . '" required>';
    echo '<button id="open-signature-popup">Click here to sign</button>'; // Add button to open signature popup
    echo '<button id="reset-signature" disabled>Reset Signature</button>'; // Add disabled button to reset signature
    echo '<input type="hidden" id="signature-data" name="signature_data" value="">';
    echo '</div>';
	echo '<div>';
	echo '<p style="font-size:14px;color:black;"><i>*Sila Masukkan Signature Anda Sebelum Membuat Bayaran</i></p>';
	echo '</div>';

    echo '<div id="signature-popup">
        <div id="signature-popup-content">
            <div id="signature-canvas-container">
                <canvas id="signature-canvas"></canvas>
            </div>
            <div id="signature-popup-buttons">
                <button id="close-signature-popup">Close</button>
                <button id="submit-signature">Submit</button>
            </div>
        </div>
    </div>';
	
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        var signatureField = document.getElementById("signature-field");
        var signaturePopup = document.getElementById("signature-popup");
        var closeButton = document.getElementById("close-signature-popup");
        var submitButton = document.getElementById("submit-signature");
        var resetButton = document.getElementById("reset-signature");
        var signatureTitle = document.getElementById("signature-title");
        var canvas = document.getElementById("signature-canvas");
        var signaturePad;

        closeButton.addEventListener("click", function(event) {
            event.preventDefault(); // Prevent form submission
            signaturePopup.style.display = "none";
        });

        submitButton.addEventListener("click", function(event) {
            event.preventDefault(); // Prevent form submission

            if (!signaturePad || signaturePad.isEmpty()) {
                console.log("Empty!");
                // Display an error message to the user
                alert("Please provide a signature.");
            } else {
                var signatureData = canvas.toDataURL();
                var signatureInput = document.getElementById("signature-data");
                signatureInput.value = signatureData;

                signaturePopup.style.display = "none";

                signatureTitle.value = signatureData;

                // Clear existing signature image
                var existingSignatureImage = document.getElementById("signature-image");
                if (existingSignatureImage) {
                    existingSignatureImage.parentNode.removeChild(existingSignatureImage);
                }

                var signatureImage = document.createElement("img");
                signatureImage.src = signatureData;
                signatureImage.alt = "Signature";
                signatureImage.id = "signature-image";
                signatureTitle.parentNode.insertBefore(signatureImage, signatureTitle.nextSibling);
            }
        });

        resetButton.addEventListener("click", function(event) {
            event.preventDefault(); // Prevent form submission

            var signatureInput = document.getElementById("signature-data");
            signatureInput.value = ""; // Clear the signature data input field

            signatureTitle.value = ""; // Remove the displayed image

            // Remove existing signature image
            var existingSignatureImage = document.getElementById("signature-image");
            if (existingSignatureImage) {
                existingSignatureImage.parentNode.removeChild(existingSignatureImage);
            }

            signaturePad.clear(); // Clear the signature canvas

            resetButton.disabled = true; // Disable the reset button after resetting the signature
        });

        var openSignaturePopup = document.getElementById("open-signature-popup");
        openSignaturePopup.addEventListener("click", function(event) {
            event.preventDefault(); // Prevent form submission
            signaturePopup.style.display = "flex";
            var canvas = document.getElementById("signature-canvas");
            signaturePad = new SignaturePad(canvas);

            canvas.addEventListener("keydown", function(event) {
                if (event.key.startsWith("Arrow")) {
                    event.preventDefault();
                }
            });

            var canvasContainer = document.getElementById("signature-canvas-container");
            var canvasWidth = canvasContainer.offsetWidth;
            var canvasHeight = canvasContainer.offsetHeight;
            canvas.width = canvasWidth;
            canvas.height = canvasHeight;
            signaturePad.clear();

            // Enable the reset button when opening the signature popup
            resetButton.disabled = false;
        });
    });
    </script>';
}
add_action( 'woocommerce_after_checkout_billing_form', 'add_signature_field_to_checkout' );

// Save signature image in order meta
function save_signature_image( $order_id ) {
    if ( isset( $_POST['signature_data'] ) ) {
        $signature_data = sanitize_text_field( $_POST['signature_data'] );
        update_post_meta( $order_id, 'signature_data', $signature_data );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'save_signature_image' );

// Display signature image in "Thank You" page and order details
function display_signature_image( $order_id ) {
    $order = wc_get_order( $order_id );

    if ( $order ) {
        $signature_data = get_post_meta( $order->get_id(), 'signature_data', true );
        if ( $signature_data ) {
            echo '<h2 class="woocommerce-order-details__edit">Signature:</h2>';
            echo '<img class="woocommerce-order-details__edit" src="' . esc_attr( $signature_data ) . '" alt="Signature">';
        }
    }
}
add_action( 'woocommerce_thankyou', 'display_signature_image', 10, 1 );

function display_order_signature_image( $order_id ) {
    $order = wc_get_order( $order_id );

    if ( $order ) {
        $signature_data = get_post_meta( $order->get_id(), 'signature_data', true );
        if ( $signature_data ) {
            echo '<h2 class="woocommerce-order-details__edit">Signature:</h2>';
            echo '<img class="woocommerce-order-details__edit" src="' . esc_attr( $signature_data ) . '" alt="Signature">';
        }
    }
}
add_action( 'woocommerce_view_order', 'display_order_signature_image', 10, 1 );

add_action( 'woocommerce_after_checkout_validation', 'validate_signature_title_field', 10, 2 );

function validate_signature_title_field( $fields, $errors ) {
    if ( empty( $_POST['signature_title'] ) ) {
        $errors->add( 'validation', __( 'Please provide a signature title.', 'woocommerce' ) );
    }
}
