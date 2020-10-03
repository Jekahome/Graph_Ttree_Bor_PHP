<?php

// Trie (prefix tree)
/*
    Бор
    Бор, также именуемый «префиксное дерево» — это древовидная структура данных,
    которая особенно эффективна при решении задач на строки.
    Она обеспечивает быстрое извлечение данных и чаще всего применяется для поиска слов в словаре,
    автозавершений в поисковике и даже для IP-маршрутизации.

Вопросы о борах, часто задаваемые на собеседованиях:
Подсчитайте общее количество слов, сохраненных в бору
Выведите на экран все слова, сохраненные в бору
Отсортируйте элементы массива при помощи бора
Постройте слова из словаря, воспользовавшись бором
Создайте словарь T9

 */
declare(strict_types=1);

namespace Trie;

class Node {

    public string $litera;
    public array $nodes;
    public bool $start;
    public bool $end;

    public function __construct(string $litera, bool $start=false, bool $end=false)
    {
        $this->litera = $litera;
        $this->start = $start;
        $this->end = $end;
        $this->nodes = [];
    }

    public function adds(array $liters){
        $count = count($liters);
        $firstLitera = array_shift($liters);
        $currentNode=null;
        foreach ($this->nodes as &$n){
            if($n->start == false && $n->end == false && $count>1 && $n->litera == $firstLitera){
                $currentNode = &$n;
                break;
            }
        }
        if(is_null($currentNode)){

            $isDubl = false;
            if(count($liters)==0){
                foreach ($this->nodes as $m){
                    if($m->litera == $firstLitera && $m->end){
                        $isDubl=true;
                    }
                }
            }
            if(!$isDubl){
                array_push($this->nodes,new Node($firstLitera,  false,count($liters)==0));
            }

            if($count>1){
                $this->nodes[count($this->nodes)-1]->adds($liters);
            }
        }else{
            $currentNode->adds($liters);
        }

    }

    public function getTree(string $stork=""):array {
        $stork=$stork.$this->litera;
        if($this->end || empty($this->nodes)){
            return [$stork];
        }

        $buff = [];
        for($i=0;$i<count($this->nodes);$i++){
            $childBuff=$this->nodes[$i]->getTree($stork);
            foreach ($childBuff as $item){
                array_push($buff,$item);
            }
        }
        return $buff;
    }

    public function getAutocompletion(array $liters):array {

        if(empty($liters)){
            if($this->end){
                return [$this->litera];
            }else{

                $buff = [];
                foreach($this->nodes as $node){
                    $temp = $node->getAutocompletion([]);
                   foreach ($temp as $t){
                       array_push($buff,$this->litera.$t);
                   }
                }
                return $buff;
            }
        }else{
            $firstLitera = array_shift($liters);
            $buff = [];
            foreach($this->nodes as $node){
                if($node->litera == $firstLitera && $node->end==false)
                {
                    $temp = $node->getAutocompletion($liters);
                    foreach ($temp as $t){
                        array_push($buff,$this->litera.$t);
                    }
                }elseif($node->end && $firstLitera==$node->litera){
                    array_push($buff,$this->litera.$firstLitera);
                }
            }
            return $buff;
        }
    }

    public function deleteTree(array $liters){

           // if($this->end)return false;

            $firstLitera = array_shift($liters);
            for ($i=0;$i<count($this->nodes) ;$i++){
                if($this->nodes[$i]->litera == $firstLitera){
                   if(empty($liters) && $this->nodes[$i]->end  /*$node->deleteTree($liters)*/){
                        unset($this->nodes[$i]);

                      if(empty($this->nodes)) {
                          $this->end = true;

                      }
                   } elseif(!empty($liters)){
                       $this->nodes[$i]->deleteTree($liters);
                       if($this->nodes[$i]->end && empty($this->nodes)){
                            unset($this->nodes[$i]);
                           $this->end = true;
                       }
                   }

                }
            }
    }

    public function getLevel(int $level,int $currentLevel,string $NodeLitera=""):?array {
        if($level==$currentLevel){

            if($NodeLitera=="" || $NodeLitera==$this->litera){
                return $this->nodes;
            }else{
                return [];
            }

        }else{
            $buff=[];
            foreach ($this->nodes as $node){
                if(!$node->end){
                    $result = $node->getLevel($level,$currentLevel+1,$NodeLitera);
                    if(!empty($result)){
                        array_push($buff,$result) ;
                    }
                }
            }
            return $buff;
        }
    }

    public function __toString()
    {
       $buff = "Node:[".$this->litera."]\t start=".($this->start?'true':'false')."\t end=".($this->end?'true':'false')."\n";
       if(!empty($this->nodes)){
           $buff.="\tnodes:\n";
           foreach ($this->nodes as $node){
               $buff.=$node->__toString();
           }
       }
       return $buff;
    }

}

class Trie {
    public array $root;
    public function __construct()
    {
        $this->root = [];
    }

    public function add(string $word){
        $liters = str_split($word,1);
        $firstLitera = array_shift($liters);

        $currentNode=null;

        foreach ($this->root as &$node){
            if($node->start == true && $node->end == false && $node->litera == $firstLitera){
                $currentNode = &$node;
                break;
            }
        }
        if(!is_null($currentNode)){
            $currentNode->adds($liters);
        }else{
            $newNode = new Node($firstLitera, true);
            $newNode->adds($liters);
            array_push($this->root,$newNode);
        }
    }

    public function list():array {
        $buf = [];
        foreach ($this->root as $node){
            $listNode = $node->getTree();
            foreach ($listNode as $item){
                array_push($buf,$item);
            }
        }
        return $buf;
    }

    public function autocompletion(string $word):array {
        $liters = str_split($word,1);
        $firstLitera = array_shift($liters);

        $searchNode = null;
        foreach ($this->root as $node){
            if($node->start == true && $node->litera == $firstLitera){
                $searchNode=$node;
                break;
            }
        }
        if(is_null($searchNode)){
            return [];
        }else{

            return $searchNode->getAutocompletion($liters);
        }
    }

    public function delete(string $word){
        $liters = str_split($word,1);
        $firstLitera = array_shift($liters);
        foreach ($this->root as $node){
            if($node->start == true && $node->litera == $firstLitera){
                $node->deleteTree($liters);
            }
        }
    }

    public function getLevel(int $level,string $NodeLitera=""):?array {
        if($level<=1){
            if($NodeLitera==""){
                return $this->root;
            }else{
                foreach ($this->root as $node){
                    if($node->litera == $NodeLitera){
                        return [$node];
                    }
                }
                return [];
            }
        }
        $buff=[];
        foreach ($this->root as $node){
            $result = $node->getLevel($level,1,$NodeLitera);
           if(!empty($result)) array_push($buff, $result) ;
        }
        return $buff;
    }

    public function printLevel(int $level,string $NodeLitera=""){
        echo "Level:$level\n";
        $this->printLevelRecursion($this->getLevel($level,$NodeLitera));
    }

    public function printLevelRecursion(array $nodes){
        foreach ($nodes as $node){
            if(is_array($node)){
                $this->printLevelRecursion($node);
            }else{
                echo "-------------------------------------------\n\n".$node;
            }
        }
    }

    public function __toString()
    {
       $buff = "";
       foreach ($this->root as $nodes){
           $buff.=$nodes;
       }
       return $buff;
    }
}

/*
$bor = new Trie();
$bor->add("tops");
$bor->add("topd");
$bor->add("waqf");

$bor->printLevel(2,"o");
*/

/*

$bor = new Trie();
$bor->add("top");
$bor->add("topsi");
$bor->add("topsa");
$bor->add("wes");

print_r( $bor->list()) ;
print_r($bor->autocompletion("top"));
*/

/*
$bor = new Trie();
$bor->add("top");
$bor->add("tops");
$bor->add("topa");
echo $bor;
$bor->delete("topa");
echo "\n\n\n";
echo $bor;
*/



$sentence = "Хеширование — это процесс, применяемый для уникальной идентификации объектов и 
сохранения каждого объекта по заранее вычисленному индексу, именуемому его ключом. 
Таким образом, объект хранится в виде ключ-значение, а коллекция таких объектов называется словарь. 
Каждый объект можно искать по его ключу. 
Существуют разные структуры данных, построенные по принципу хеширования, но чаще всего из таких структур применяется хеш-таблица.";

$sentence = preg_replace('/[.,\n]/i', '', $sentence);
$sentence = explode(" ",$sentence);
$words = [];
foreach ($sentence as $word){
    $word = trim(strtoupper($word));
    if(strlen($word)>1){
        array_push($words,$word);
    }
}
$bor = new Trie();
foreach ($words as $word){
    $bor->add($word);
}
print_r($bor->autocompletion("пр"));
