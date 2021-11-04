<?php

namespace App\Repository;

use App\Entity\Dependency;
use Ramsey\Uuid\Uuid;

class DependencyRepository 
{

    private $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
    }
   
    private function getDependecies(){
        $path = $this->rootPath . '/composer.json';
        $json = json_decode(file_get_contents($path),true);
        return $json['require'];
    }

    /**
     * @return Dependency[]
     */
    public function findAll(): array {
        $items = [];
        foreach($this->getDependecies() as $name => $version){
            $items[] = new Dependency( $name, $version);
        }
        return $items;
    }

    public function find(string $uuid): ?Dependency 
    {
        $dependencies = $this->getDependecies();
        foreach($this->findAll() as $dependency)
        {
            if($dependency->getUuid() == $uuid){
                return $dependency;
            }
        }
        return null;
    }
    
    public function persist(Dependency $depency){
        $path = $this->rootPath . '/composer.json';
        $json = json_decode(file_get_contents($path),true);
        $json['require'][$depency->getName()] = $depency->getVersion();
        file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    }

    public function remove(Dependency $depency){
        $path = $this->rootPath . '/composer.json';
        $json = json_decode(file_get_contents($path),true);
        unset($json['require'][$depency->getName()]);
        file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
