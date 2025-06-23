<?php
class Hobby extends EMongoEmbeddedDocument
{
    public $name;
    public $description;

    public function rules()
    {
        return array(
            array('name', 'required'),
            array('name', 'length', 'max' => 50),
            array('name,description', 'safe'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Hobby Name',
            'description' => 'Hobby Description',
        );
    }
}
