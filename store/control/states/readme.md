
# STATES / COUNTIES / OTHER
==========================================

For specific population of states, counties etc for shipping / billing addresses
If none are set, displays standard input box..

This is ideal for payment gateways that require a certain value in the states box.

-------------------------------------------------------------------------------------------------------------------------

To set options, create new .php file in 'control/states/' directory. File should be country ID in database.
For example, Turkey would be 177.php etc

Array should be called $mc_states with key/value pairs. Example:

$mc_states = array(
  'val' => 'Text here..',
  'val2' => 'Text here..',
  'val3' => 'Text here..'
);

etc