<?php

namespace Level3\Resource\Format\Reader;

use Level3\Resource\Format\Reader;
use XMLReader as BasicXMLReader;

abstract class XMLReader extends Reader
{
    public function execute($input)
    {
        $reader = new BasicXMLReader();
        $reader->xml($input);
        $array = $this->xmlToArray($reader);

        return $this->arrayToResource($array);
    }

    abstract protected function arrayToResource(Array $array);

    protected function xmlToArray(BasicXMLReader $xml){ 
        $array = []; 

        $i = 0; 
        while($xml->read()){ 
            if ($xml->nodeType == BasicXMLReader::END_ELEMENT) {
                break; 
            } 

            if ($xml->nodeType == BasicXMLReader::ELEMENT && !$xml->isEmptyElement) { 
                $array[$i]['name'] = $xml->name; 
                $array[$i]['values'] = $this->xmlToArray($xml); 
                $array[$i]['attributes'] = $this->getAttributesFromNode($xml);
                $i++; 
            } else if ($xml->isEmptyElement) { 
                $array[$i]['name'] = $xml->name; 
                $array[$i]['values'] = null; 
                $array[$i]['attributes'] = $this->getAttributesFromNode($xml);
                $i++;
            } else if($xml->nodeType == BasicXMLReader::TEXT) {
                $array = $xml->value; 
            }
        }

        return $array; 
    }

    private function getAttributesFromNode(BasicXMLReader $xml)
    {
        if(!$xml->hasAttributes) {
            return null;
        }

        $attributes = [];
        while($xml->moveToNextAttribute()) {
            $attributes[$xml->name] = $xml->value; 
        }
        
        return $attributes;
    }


}
