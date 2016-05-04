<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'image_path' => 'http://directcars.in/images/',
	'image_absolute_path' => '/var/www/html/public/images/',
	'public_folder_path' => "/var/www/html/public/",
	'website_url' => 'https://directcars.in',
);