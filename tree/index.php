<?php

declare(strict_types=1);

// Tree (data structure) https://habr.com/ru/post/267855/

/*
ЗАЧЕМ использовать https://www.math.spbu.ru/user/dlebedin/ncpp13.pdf

Самыми простыми решениями задачи построения ассоциативного массива являются списки и массивы пар (ключ,данные).
Однако эти решения обладают рядом недостатков.
Списки обеспечивают быструю вставку и удаление элементов из произвольной позиции (при условии, что мы знаем
указатель на элемент в требуемой позиции), но поддерживают только линейный поиск, что в большинстве случаев
неприемлемо медленно.
Массивы могут обеспечить быстрый (двоичный или интерполяционный) поиск, но для этого массив должен быть
упорядочен, и вставка в упорядоченный массив требует сдвига большого числа элементов, что также неприемлемо медленно.
Дальнейшее развитие технологии предлагает использовать для построения ассоциативных массивов деревья.

Свойство двоичного дерева поиска позволяет организовать поиск в таком дереве по тому же принципу, как и
двоичный поиск в отсортированном массиве (почему собственно такое дерево и называется двоичным деревом поиска),
и время, затрачиваемое на основные операции с таким деревом (вставка/поиск/удаление пары) определяется не количеством элементов в дереве, а его высотой.

*/
// Бинарное дерево - эмулирующая древовидную структуру в виде набора связанных узлов. Является связным графом, не содержащим циклы.
// Деревья подобны графам, однако, ключевое отличие дерева от графа таково: в дереве не бывает циклов.

//Существуют деревья следующих типов:
    // - N-арное дерево
    // - Сбалансированное дерево — это бинарное дерево поиска с логарифмической высотой. https://habr.com/ru/post/150732/
    // - Двоичное дерево
    // - Двоичное дерево поиска
    // - АВЛ-дерево
    // - Красно-черное дерево
    // - 2—3 дерево

//Из вышеперечисленных деревьев чаще всего используются 'двоичное дерево' и 'двоичное дерево поиска'.

//Вопросы о деревьях, часто задаваемые на собеседованиях:

//Найдите высоту двоичного дерева
//Найдите k-ное максимальное значение в двоичном дереве поиска
//Найдите узлы, расположенные на расстоянии “k” от корня
//Найдите предков заданного узла в двоичном дереве
//----------------------------------------------------------------------------------------------------------------------


/*
 Двоичное дерево поиска (англ. binary search tree, BST) — это двоичное дерево, для которого выполняются следующие дополнительные условия (свойства дерева поиска):



Оба поддерева — левое и правое — являются двоичными деревьями поиска.
У всех узлов левого поддерева произвольного узла X значения ключей данных меньше либо равны, нежели значение ключа данных самого узла X.
У всех узлов правого поддерева произвольного узла X значения ключей данных больше либо равны, нежели значение ключа данных самого узла X.
Очевидно, данные в каждом узле должны обладать ключами, на которых определена операция сравнения меньше.

У каждого узла не более двух детей.
Любое значение меньше значения узла становится левым ребенком или ребенком левого ребенка.
Любое значение больше или равное значению узла становится правым ребенком или ребенком правого ребенка.





Как правило, информация, представляющая каждый узел, является записью, а не единственным полем данных. Однако это касается реализации, а не природы двоичного дерева поиска.[2]

Для целей реализации двоичное дерево поиска можно определить так:

Двоичное дерево состоит из узлов (вершин) — записей вида (data, left, right),
где data — некоторые данные, привязанные к узлу, left и right — ссылки на узлы, являющиеся детьми данного узла — левый и правый сыновья соответственно.
Для оптимизации алгоритмов конкретные реализации предполагают также определения поля parent в каждом узле (кроме корневого) — ссылки на родительский элемент.
Данные (data) обладают ключом (key), на котором определена операция сравнения «меньше».
В конкретных реализациях это может быть пара (key, value) — (ключ и значение), или ссылка на такую пару, или простое определение операции сравнения на необходимой структуре данных или ссылке на неё.
Для любого узла X выполняются свойства дерева поиска: key[left[X]] < key[X] ≤ key[right[X]],
то есть ключи данных родительского узла больше ключей данных левого сына и нестрого меньше ключей данных правого.

Основным преимуществом двоичного дерева поиска перед другими структурами данных является возможная высокая эффективность реализации основанных на нём алгоритмов поиска и сортировки.

Двоичное дерево поиска применяется для построения более абстрактных структур, таких, как множества, мультимножества, ассоциативные массивы.


Про ЛВЛ сбалансированные деревья
Однако, двоичное дерево поиска также имеет один существенный недостаток. Дело в том, что при фиксированном
наборе ключей основное свойство двоичного дерева поиска не определяет структуру дерева однозначно, и в худшем
случае такое дерево может вырождаться в список (например, у каждого узла нет левого сына). Так происходит, если
мы добавляем элементы в ассоциативный массив в порядке возрастания ключей. В этом случае двоичное дерево
поиска ничем не отличается от обычного связного списка со всеми его недостатками.
Для исправления такой ситуации было предложено понятие сбалансированного дерева.
В самом жестком варианте дерево сбалансировано, если число узлов в левом и правом поддереве каждого узла отличается максимум на 1.
Однако, такой подход обладает недостатками упорядоченного массива — в дерево с такими требованиями очень
трудно добавить новый узел; точнее, добавить просто, но это может привести к нарушению условия сбалансированности, а вот восстановить такую сбалансированность — очень трудно и долго.
Поэтому в итоге решение проблемы состоит в том, чтобы ослабить требование сбалансированности.
Известно два варианта такого ослабления.
Первый вариант был предложен в 1962 году Г. М. Адельсоном-Вельским и Е. М. Ландисом, по первым буквам
фамилий которых и получили свое название АВЛ-деревья. Их предложение состояло в том, чтобы ослабить требование
сбалансированности, заменив его АВЛ-условием: высоты поддеревьев любого узла должны отличаться не более, чем на 1.
Оказывается, во-первых, этого достаточно, чтобы высота дерева не превосходила C log2 N, где N — число узлов
дерева и C — ни от чего не зависящая константа. Во-вторых, после добавления или удаления из дерева одного узла
справедливость АВЛ-условия может быть восстановлена за время, ограниченное другой константой, умноженной на высоту дерева.
 */
namespace BinarySearchTree;

class Node{
    public Node $left;
    public Node $right;
    public int $key;
    public string $value;
    public int $level;
    public function __construct(int $key,string $value="",int $level)
    {
        $this->key = $key;
        $this->value = $value;
        $this->level = $level;

    }

    public function add($key,$value,$level){
        //  key[left[X]] < key[X] ≤ key[right[X]]
        if($key<$this->key){
            if(!isset($this->left)){
                $this->left = new Node($key,$value,$level+1);
            }else{
                $this->left->add($key,$value,$level+1);
            }
        }else{
            if(!isset( $this->right)){
                $this->right = new Node($key,$value,$level+1);
            }else{
                $this->right->add($key,$value,$level+1);
            }
        }
    }

    private function setLeft(Node &$left):bool{
         if (isset($this->left)){
            return $this->left->setLeft($left);
         }else{
             $this->left = $left;
             return true;
         }
    }

    private function setRight(Node &$right):bool{
        if (isset($this->right)){
            return $this->right->setRight($right);
        }else{
            $this->right = $right;
            return true;
        }
    }

    public function remove(int $key):bool{

        //1. У удаляемого узла нет правого ребенка
        // В этом случае мы просто перемещаем левого ребенка (при его наличии) на место удаляемого узла.

        //2. У удаляемого узла есть правый ребенок, у которого, в свою очередь нет левого ребенка.
        // В этом случае мы просто перемещаем правого ребенка на место удаляемого узла, если был левый ребенок то его так же цепляем к правому ребенку

        //3. У удаляемого узла есть правый ребенок, у которого, в свою очередь есть левый ребенок.
        // В этом случае этого 'левого ребенка' мы ставим на место удаляемого узла, а детям удаляемого узла ищем место в потомках того 'левого ребенка'

        if($key < $this->key){
            if(isset($this->left)){
                if($this->left->key != $key){
                    return $this->left->remove($key);
                }else{
                    // дублирование кода, из-за ошибки работы со сылками
                    // т.е. дать общую ссылку $ref в зависимости от выбранного узла left/right и потом с ней работать - не получилось

                    if(!isset($this->left->right)){
                        if(isset($this->left->left)){
                            $left = $this->left->left;
                            unset($this->left->left);
                            $this->left = $left;
                        }else{
                            unset($this->left);
                        }
                        return true;
                    }elseif(isset($this->left->right) && !isset($this->left->right->left)){
                        if(isset($this->left->left)){
                            $this->left->right->left = $this->left->left;
                            unset($this->left->left);
                        }
                        $this->left = $this->left->right;
                        return true;
                    }elseif(isset($this->left->right) && isset($this->left->right->left)){
                        if(isset($this->left->left)){
                            if(!isset($this->left->right->left->left)){
                                $this->left->right->left->left = $this->left->left;
                                unset($this->left->left);
                            }else{
                                $left = $this->left->left;
                                // найти в этой ветке место для вставки ниже
                                $this->left->right->left->left->setLeft($left);
                                unset($this->left->left);
                            }
                        }
                        if(!isset($this->left->right->left->right)){
                            $link = $this->left->right->left;
                            $link2 = $this->left->right;
                            unset($this->left->right->left);
                            unset($this->left->right);
                            $this->left = $link;
                            $this->left->right=$link2;
                        }else{
                            $link = $this->left->right->left;
                            $link2 = $this->left->right;
                            unset($this->left->right->left);
                            unset($this->left->right);
                            $this->left = $link;
                            // найти в этой ветке место для вставки ниже
                            $this->left->right->setRight($link2);
                        }
                        return true;
                    }
                }
            }else{
                return false;
            }
        }else{
            if(isset($this->right)){
                if($this->right->key != $key){
                    return $this->right->remove($key);
                }else{
                    // дублирование кода, из-за ошибки работы со сылками
                    if(!isset($this->right->right)){
                        if(isset($this->right->left)){
                            $right = $this->right->left;
                            unset($this->right->left);
                            $this->right = $right;
                        }else{
                            unset($this->right);
                        }
                        return true;
                    }elseif(isset($this->right->right) && !isset($this->right->right->left)){
                        if(isset($this->right->left)){
                            $this->right->right->left = $this->right->left;
                            unset($this->right->left);
                        }
                        $this->right = $this->right->right;
                        return true;
                    }elseif(isset($this->right->right) && isset($this->right->right->left)){
                        if(isset($this->right->left)){
                            if(!isset($this->right->right->left->left)){
                                $this->right->right->left->left = $this->right->left;
                                unset($this->right->left);
                            }else{
                                $right= $this->right->left;
                                // найти в этой ветке место для вставки ниже
                                $this->right->right->left->left->setLeft($right);
                                unset($this->left->left);
                            }
                        }
                        if(!isset($this->right->right->left->right)){
                            $link = $this->right->right->left;
                            $link2 = $this->right->right;
                            unset($this->right->right->left);
                            unset($this->right->right);
                            $this->right = $link;
                            $this->right->right=$link2;
                        }else{
                            $link = $this->right->right->left;
                            $link2 = $this->right->right;
                            unset($this->right->right->left);
                            unset($this->right->right);
                            $this->right = $link;
                            // найти в этой ветке место для вставки ниже
                            $this->right->right->setRight($link2);
                        }
                        return true;
                    }
                }
            }else{
                return false;
            }
        }
        return false;
    }

    public function height(int $height=0):int{
        $countLeft=0;
        $countRight=0;
        if(isset($this->left)){
            $countLeft = $this->left->height($height+1);
        }else{
            $countLeft = $height;
        }
        if(isset($this->right)){
            $countRight =  $this->right->height($height+1);
        }else{
            $countRight = $height;
        }
        return $countLeft>=$countRight?$countLeft:$countRight;
    }

    public function minKey():?int{
        $minKey = null;
        if(isset($this->left)){
            $minKey = $this->left->minKey();
        }else{
            $minKey = $this->key;
        }
        return $minKey;
    }

    public function maxKey():?int{
        $maxKey = null;
        if(isset($this->right)){
            $maxKey = $this->right->maxKey();
        }else{
            $maxKey = $this->key;
        }
        return $maxKey;
    }

    public function getAllNodes():array {
        $currentNode = ['key'=>$this->key,'level'=>$this->level,'left'=>null,'right'=>null];

        if(isset($this->left)){
            $currentNode['left']=$this->left->getAllNodes();
        }
        if(isset($this->right)){
            $currentNode['right']=$this->right->getAllNodes();
        }
        return $currentNode;
    }

    public function searchValue(string $value):?array {
        $nodes = [];
        if($this->value == $value){
            array_push( $nodes,['key'=>$this->key,'value'=>$this->value,'level'=>$this->level]);
        }

        if(isset($this->left)){
            $result = $this->left->searchValue($value);
            if(!empty($result)){
                foreach ($result as $item){
                    array_push($nodes,$item);
                }
            }
        }
        if(isset($this->right)){
            $result = $this->right->searchValue($value);
            if(!empty($result)){
                foreach ($result as $item){
                    array_push($nodes,$item);
                }
            }
        }

        return $nodes;
    }

    public function searchKey(int $key):?array {
           $nodes = [];
            if($this->key == $key){
                array_push( $nodes,['key'=>$this->key,'value'=>$this->value,'level'=>$this->level]);
            }
            if( $key < $this->key ){
                if( isset($this->left)){
                   $result = $this->left->searchKey($key);
                   if(!empty($result)){
                       foreach ($result as $item){
                           array_push($nodes,$item);
                       }
                   }
                }
            }else{
                if( isset($this->right)){
                    $result = $this->right->searchKey($key);
                    if(!empty($result)){
                        foreach ($result as $item){
                            array_push($nodes,$item);
                        }
                    }
                }
            }
        return $nodes;
    }

    public function __toString()
    {
        $buff="";
        $level = $this->level;
        $buff.="LEVEL:$level ";
        $buff.="KEY:{$this->key} VALUE:{$this->value}\n";
        $buff.= sprintf("%' {$level}s\n", "LEFT:".(isset($this->left)?$this->left:"null"));
        $buff.= sprintf("%' {$level}s\n", "RIGHT:".(isset($this->right)?$this->right:"null"));
       return $buff;
    }
}


class BinarySearchTree{

    public Node $root;

    public function insert(int $key,string $value=""){
        if(!isset( $this->root)){
            $this->root = new Node($key,$value,0);
        }else{
            $this->root->add($key,$value,0);
        }
    }

    public function remove(int $key){
        if($this->root->key==$key){
            $this->root->left=null;
            $this->root->right=null;
        }else{
            $this->root->remove($key);
        }
    }

    public function height():int{
        if(isset( $this->root)){
            return $this->root->height();
        }else{
            return 0;
        }
    }

    public function minKey():int{
        if(isset( $this->root)){
            return $this->root->minKey();
        }else{
            return 0;
        }
    }

    public function maxKey():int{
        if(isset( $this->root)){
            return $this->root->maxKey();
        }else{
            return 0;
        }
    }

    public function getAllNodes():array {
        if(isset( $this->root)){
            $currentNode = ['Root key'=>$this->root->key,'level'=>$this->root->level,'left'=>null,'right'=>null];

            if(isset($this->root->left)){
                $currentNode['left']=$this->root->left->getAllNodes();
            }
            if(isset($this->right)){
                $currentNode['right']=$this->root->right->getAllNodes();
            }
            return $currentNode;
        }else{
            return [];
        }
    }

    public function searchValue(string $value):?array {
        if(isset( $this->root)){
            if($this->root->value == $value)
                return ['root key'=>$this->root->key,'value'=>$this->root->value,'level'=>$this->root->level];
            return $this->root->searchValue($value);
        }else{
            return null;
        }
    }

    public function searchKey(int $key):?array {
        if(isset( $this->root)){
            if($this->root->key == $key)
                return ['root key'=>$this->root->key,'value'=>$this->root->value,'level'=>$this->root->level];
            return $this->root->searchKey($key);
        }else{
            return null;
        }
    }

    public function __toString()
    {
        $buff="".(isset($this->root)?$this->root:"null");
        return $buff;
    }
}

function example(){
    $tree = new BinarySearchTree();
    $tree->insert(80,"80");
    $tree->insert(50,"50");
    $tree->insert(100,"100");
    $tree->insert(20,"20");
    $tree->insert(70,"70");$tree->insert(70,"702");
    $tree->insert(68,"68");
    $tree->insert(67,"67");
    $tree->insert(69,"69");
    $tree->insert(66,"66");

    $tree->remove(50);

    echo "HEIGHT TREE:".($tree->height())."\n";
    echo "MIN KEY:".$tree->minKey()."\n";
    echo "MAX KEY:".$tree->maxKey()."\n";


    print_r($tree->searchKey(70));
    print_r($tree->searchValue("702"));

    print_r($tree->getAllNodes());
}




// TEST ==============================================================================================================
// сравнение своего дерева с реализацией массива в php, которое так же дерево


$tree = new BinarySearchTree();
define("COUNT_ITERATION", 1000000);

// INSERT
// tree 18.48166
// map 0.53320

/*
define("START_TIME", microtime(true));
for($i=0;$i<COUNT_ITERATION;$i++){
    $r = mt_rand(1,COUNT_ITERATION);
    $tree->insert( $r,"{$r}");
}

printf("Время работы скрипта: %.5f с", microtime(true)-START_TIME);// 18.48166
*/

// ------------------------------------------------------------------------------------------------------------------
/*
define("START_TIME", microtime(true));
$map = [];
for($i=0;$i<COUNT_ITERATION;$i++){
    $r = mt_rand(1,COUNT_ITERATION);
    $map[$r]="{$r}";
}
printf("Время работы скрипта: %.5f с", microtime(true)-START_TIME);//  0.53320
*/
//===================================================================================================================

// SEARHC VALUE
// tree 1.31766
// map 0.00897


$search = "";
for($i=0;$i<COUNT_ITERATION;$i++){

    $r = mt_rand(1,COUNT_ITERATION);
    if($i==COUNT_ITERATION/2){$search="{$r}";};
    $tree->insert( $r,"{$r}");
}
define("START_TIME", microtime(true));
$result = $tree->searchValue($search);
assert(!empty($result));
printf("Время работы скрипта: %.5f с", microtime(true)-START_TIME);//  1.31766

// ------------------------------------------------------------------------------------------------------------------

/*
$map = [];
$search = "";
for($i=0;$i<COUNT_ITERATION;$i++){
    $r = mt_rand(1,COUNT_ITERATION);
    if($i==COUNT_ITERATION/2){$search="{$r}";};
    $map[$r]="{$r}";
}
define("START_TIME", microtime(true));
$result = array_search($search, $map);
assert(!empty($result));
printf("Время работы скрипта: %.5f с", microtime(true)-START_TIME);// 0.00897

*/
