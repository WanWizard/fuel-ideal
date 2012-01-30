## DISCLAIMER

As of now, this package has only been used against the simulator. Do **NOT** use in the _develop_ branch in a production environment!

# Ideal

Ideal provides an interface to the Dutch iDEAL bank payment system in a package for FuelPHP.

The package supports both basic and professional modes, and currently provide support for Rabobank, ING bank and ABN-AMRO bank.
It also provides a simulator mode which allows you to test your code without needing an iDEAL bank contract.

# Install

to install this package simply add this source to your package configuration file:

	http://github.com/WanWizard

and then you can simply type in:

	php oil package install ideal

# Configure

The package contains default configuration files both both the package (ideal.php) and all supported banks. Copy the package
configuration file and your banks configuration file to app/config before making modifications.

The public certificates of the supported banks are included in the package. Based on the bank you do business with you will
have to replace them. You also need to generate your own private key and certificate in case you are going to use the
professional services interface of the package.

# Testing

The package supports 'simulator' as bank, in which case you can test transactions using http://www.ideal-simulator.nl. The configuration
of all supported banks support both a test mode and a transaction module. All banks require you to have a contract before you can use
their test facilities, and some of them require you to run some specific tests in order to get access to the production service.

# Usage: Basic services

## Basic services

Some banks provide basic services, sometimes called _Simple_, _Lite_ or _Basis_. With this service, the bank will handle the complete
transaction, and will only send a basic result back by redirecting to one of three URL's after the transaction. There is no access to
transaction information, you have to login to your bank's dashboard to see those.

### public function fieldset($fieldset = null)

Generates the fieldset with all required fields. All your application has to do is add a submit button to it, and echo it in a view.
Before you do, make sure the **success_url**, **cancel_url** and **error_url** have been defined, as that is where the service redirects to after
the transaction has been processed. If not defined, they default to the current url. The status can be retrieved using Input::get('status').

If you pass a Fielset instance, the fields will be added to that instance. If not, a new instance will be created.

### public function set_amount($amount)

Sets the amount to be paid. $amount is an integer or a float, the value is defined in euro's (not in cents!)

### public function set_description($description)

Sets a description for the transaction (string, maximum 32 characters)

### public function set_reference($reference)

Sets a transaction reference for the transaction (string, maximum 16 characters)

### public function set_merchant($merchantid, $subid = '0')

Sets your merchant ID, and optionally your Sub ID. Normally this is set in the configuation file.

### public function set_hashkey($hashkey)

Sets your hash key or transaction password.  Normally this is set in the configuation file.

### public function set_success_url($url)

Sets the URL to return to if the transaction was succesfully completed. A default can be set in the configuration file.

### public function set_cancel_url($url)

Sets the URL to return to if the transaction was cancelled by the user. A default can be set in the configuration file.

### public function set_error_url($url)

Sets the URL to return to if the transaction was cancelled due to an error. A default can be set in the configuration file.

## Professional services

Professional services provides a full transaction interface. Transactions are processed asynchronous and are assigned a transaction id.
At any time you can request the status of a transaction using the assigned transaction id. The status returned includes detailed information
about the transaction, including basic account information of the user.

## public function get_issuers()

Retrieves a list of issuers supported by the bank's acquirer service. Returns an associative array with bank ID's and names. You need the
ID of the bank selected by the user to prepare the transaction.

## public function transaction_prepare()

Prepares the transaction and sets it to the bank. You need to set all required fields (issuer, amount, description, and reference) before calling this method.
If the preparation was a success, this method returns the assigned transaction ID. You need to store this to be able to request status updates for this
transaction.

## public function transaction_execute()

Redirects to the bank to allow the user to finish the prepared transaction. After the transaction is complete the bank redirects back to your application
using the **return_url** set.

## public function transaction_status($transaction_id)

Retrieves a status update for the given transaction id, returned by a transaction_prepare() call. If succesful an array is returned which includes the
transaction id and status, and information about the account holder (consumer) that requested the transaction.

### public function set_amount($amount)

Sets the amount to be paid. $amount is an integer or a float, the value is defined in euro's (not in cents!)

### public function set_issuer($issuer)

Sets the issuer you want to contact for the transaction. This is the issuer ID returned by get_issuers() and selected by the user.

### public function set_description($description)

Sets a description for the transaction (string, maximum 32 characters)

### public function set_reference($reference)

Sets a transaction reference for the transaction (string, maximum 16 characters)

### public function set_merchant($merchantid, $subid = '0')

Sets your merchant ID, and optionally your Sub ID. Normally this is set in the configuation file.

### public function set_return_url($url)

Sets the URL to return after the transaction was executed. You need to call transaction_status() to get the status of the transaction.
A default can be set in the configuration file.
