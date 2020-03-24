<?php

class slugCache {
    private $slugs              = [
        "get"    => [],
        "post"   => [],
        "put"    => [],
        "delete" => []
    ];
    private $regexSlugs              = [
        "get"    => [],
        "post"   => [],
        "put"    => [],
        "delete" => []
    ];
    private $variablesFromSlugs = [];

    public function addSlug($type, $slug, $callback) {
        if(!strpos($slug,'/:')) {
            $this->slugs[$type][$slug] = $callback;
        }
        else {
            $regexObject          = [];
            $slugTempArray        = [];
            $regexNames           = [];
            $regexObject['regex'] = '/^';
            $slugArray            = explode('/', $slug);
            for ($i=0; $i < count($slugArray); $i++) { 
                if($slugArray[$i][0] != ':') {
                    $slugTempArray[] = $slugArray[$i];
                }
                else {
                    $slugTempArray[] = '([a-z0-9]*)';
                    $regexNames[]    = substr($slugArray[$i],1);
                }
            }
            $regexObject['regex'] = $regexObject['regex'] . implode('\/',$slugTempArray);
            $regexObject['regex'] = $regexObject['regex'] . '$/';
            $regexObject['regexNames'] = $regexNames;
            $regexObject['callback'] = $callback;
            $this->egexSlugs[$type][] = $regexObject;
        }
    }

    public function loadSlugCallback($method,$path) {
        if($this->slugs[$method][$path]) {
            return $this->slugs[$method][$path];
        }

        for ($i=0; $i < count($this->regexSlugs[$method]); $i++) { 
            if(preg_match($this->regexSlugs[$method][$i]['regex'],$path,$matches)) {
                for ($index=0; $index < count($this->regexSlugs[$method][$i]['regexNames']); $index++) { 
                    $this->variablesFromSlugs[$this->regexSlugs[$method][$i]['regexNames'][$index]] = $matches[$index+1];
                }
                return $this->regexSlugs[$method][$i]['callback'];
            }
        }
    }

    private function regexGeneration($slug) {

    }

    public function getSlugVariable($regexName) {
        return $this->variablesFromSlugs[$regexName];
    }
}