<?php
class Address extends EMongoEmbeddedDocument
{
    public $address_line1;
    public $address_line2;
    public $city;
    public $state;
    public $zip;
    public $country;

    public function rules()
    {
        return array(
            array('address_line1, city, state, zip, country', 'required'),
            array('zip', 'match', 'pattern' => '/^[0-9\-]{4,10}$/'),
            array('address_line1, address_line2, city, state, country', 'length', 'max'=>100),
        );
    }

    public function attributeLabels()
    {
        return array(
            'address_line1' => 'Address Line 1',
            'address_line2' => 'Address Line 2',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'ZIP Code',
            'country' => 'Country',
        );
    }
}
