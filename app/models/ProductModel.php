<?php
class ProductModel
{
    private $ID;
    private $Name;
    private $Description;
    private $Price;
    private $Image;

    public function __construct($ID, $Name, $Description, $Price, $Image = 'default.png')
    {
        $this->ID = $ID;
        $this->Name = $Name;
        $this->Description = $Description;
        $this->Price = $Price;
        $this->Image = $Image;
    }

    public function getID()
    {
        return $this->ID;
    }
    public function setID($ID)
    {
        $this->ID = $ID;
    }

    public function getName()
    {
        return $this->Name;
    }
    public function setName($Name)
    {
        $this->Name = $Name;
    }

    public function getDescription()
    {
        return $this->Description;
    }
    public function setDescription($Description)
    {
        $this->Description = $Description;
    }

    public function getPrice()
    {
        return $this->Price;
    }
    public function setPrice($Price)
    {
        $this->Price = $Price;
    }

    public function getImage()
    {
        return $this->Image;
    }
    public function setImage($Image)
    {
        $this->Image = $Image;
    }
}
?>