<?php

namespace Mobbel\TinyRest;

class TinyRest {
    private $slugCache = null;
    private $method    = null;
    private $path      = null;

    public function __construct() {
        if($this->slugCache == null) {
            include('libs/slugCache.php');
            $this->slugCache = new slugCache();
        }
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path    = ltrim(parse_url($_SERVER['REQUEST_URI'])['path'], '/');
    }

    public function get($slug, $callback) {
        $this->slugCache->addSlug('get', $slug, $callback);
    }
    public function post($slug, $callback) {
        $this->slugCache->addSlug('post', $slug, $callback);
    }
    public function put($slug, $callback) {
        $this->slugCache->addSlug('put', $slug, $callback);
    }
    public function delete($slug, $callback) {
        $this->slugCache->addSlug('delete', $slug, $callback);
    }

    public function fromSlug($regexName) {
        return $this->slugCache->getSlugVariable($regexName);
    }

    public function render() {
        if($this->method != 'OPTIONS'){
            if(isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'form-data') !== false) {
                $requestBody = $_POST;
            }
            else if($this->method != 'GET'){
                $requestBody = json_decode(file_get_contents('php://input'));
            }
            else {
                $requestBody = $_GET;
            }
    
            $callback = $this->slugCache->loadSlugCallback(strtolower($this->method), $this->path);
            return $callback($requestBody);
        }
        else {
            header( "HTTP/1.1 200 OK" );
            exit;
        }
    }
}