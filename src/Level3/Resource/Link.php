<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Resource;

class Link
{
    protected $href;
    protected $templated;
    protected $type;

    protected $name;
    protected $hreflang;
    protected $title;

    public function __construct($href = null)
    {
        $this->href = $href;
    }

    public function setHref($href)
    {
        $this->href = $href;

        return $this;
    }

    public function getHref()
    {
        return $this->href;
    }

    public function setTemplated($templated)
    {
        $this->templated = (boolean) $templated;

        return $this;
    }

    public function isTemplated()
    {
        return $this->templated;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setLang($lang)
    {
        $this->hreflang = $lang;

        return $this;
    }

    public function getLang()
    {
        return $this->hreflang;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getAttributes()
    {
        $attr = get_object_vars($this);
        unset($attr['href']);

        return array_filter($attr);
    }

    public function toArray()
    {
        $filter = function($value) {
            return $value != null;
        };

        return array_filter(get_object_vars($this), $filter);
    }

    public function __toString()
    {
        return $this->getHref();
    }
}
