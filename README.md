<h1 align="center">Sprinix_Skip2FAByIP</h1> 


Sprinix_Skip2FAByIP is a Magento 2 module that allows skipping Two-Factor Authentication (2FA) based on IP address. This module provides a configuration option to specify a list of IP addresses that are exempted from the 2FA requirement, making it convenient for trusted IP addresses to bypass the additional authentication step.

## Installation

1. Copy the contents of this repository to the `app/code/Sprinix/Skip2FAByIP` directory of your Magento 2 installation.
2. OR run command `composer require sprinix/skip2fa-by-ip` to install using composer
3. Run the following command from the Magento root directory:

```
bin/magento module:enable Sprinix_Skip2FAByIP
bin/magento setup:upgrade
bin/magento cache:clean
```

3. Log out and log back into the Magento Admin panel for the changes to take effect.

## Configuration

1. In the Magento Admin panel, go to **Stores** > **Configuration** > **Security** > **2FA**.
2. Enable the setting **Skip 2FA By IP**
3. Click **Save Config**.

## Usage

Once the module is enabled and configured, any requests originating from the specified IP addresses will bypass the Two-Factor Authentication requirement. Please ensure that you only add trusted IP addresses to the allowed list.

*Command to allow IPs*

` bin/magento config:set twofactorauth/general/allowed_ips 127.0.0.1`

*Clean Magento cache*

` bin/magento cache:clean`

## Compatibility

This module is compatible with Magento 2.4.x versions.

This module supports proxy detection as well, you can also define your client IP HTTP header in the configuration as well.

This module respects headers from cloudflare.

## Contribution

Contributions are welcome! If you encounter any issues or have suggestions for improvements, please feel free to open an issue or submit a pull request.

## License

This module is released under the [MIT License](https://opensource.org/licenses/MIT).

## Author Information

Sprinix_Skip2FAByIP module is developed and maintained by [Gulshan Kumar Maurya](https://github.com/gulshankumar).
