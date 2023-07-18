# Signature Field for WooCommerce Checkout

This repository contains code that adds a signature field to the WooCommerce checkout process. Customers can digitally sign using a canvas element, and the signature data is saved in the order meta. The signature image is displayed on the "Thank You" page and in the order details.

<img width="1243" alt="image" src="https://github.com/khairulimran-97/Signature-Checkout-Woocommerce/assets/105085586/fdcd98b4-786e-4568-8741-5c4ac00080c9">


## Requirements

- WordPress
- WooCommerce

## Installation

1. Download the repository as a ZIP file.
2. Extract the ZIP file.
3. Copy the extracted folder to the `wp-content/plugins/` directory of your WordPress installation.
4. Activate the plugin from the WordPress admin panel.

## Usage

1. During the checkout process, customers will see a "Tandatangan Pemohon" section with a signature field.
2. To sign, customers need to click the "Click here to sign" button, which opens a signature popup.
3. Customers can use the mouse or touch input to draw their signature on the canvas.
4. After signing, customers need to click the "Submit" button in the signature popup.
5. The signature image will be displayed in the "Thank You" page and in the order details.
6. Customers can download the signature image by clicking the "Download Signature" link.

## Customization

You can customize the appearance and behavior of the signature field by modifying the code in the plugin files. Make sure to follow best practices and test any changes thoroughly.

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE).
