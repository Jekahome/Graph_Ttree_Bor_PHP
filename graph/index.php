<?php
/*
Графы

Граф — это множество узлов, соединенных друг с другом в виде сети. Узлы также называются вершинами. Пара (x,y) называется ребром, это означает, что вершина x соединена с вершиной y. Ребро может иметь вес/стоимость — показатель, характеризующий, насколько затратен переход от вершины x к вершине y.


Типы графов:

Неориентированный граф
Ориентированный граф

В языке программирования графы могут быть двух видов:

Матрица смежности
Список смежности

Распространенные алгоритмы обхода графа:

Поиск в ширину
Поиск в глубину

Вопросы о графах, часто задаваемые на собеседованиях:

Реализуйте поиск в ширину и поиск в глубину
Проверьте, является граф деревом или нет
Подсчитайте количество ребер в графе
Найдите кратчайший путь между двумя вершинами


Граф – это множество вершин и ребер. Ребро – это связь между двумя вершинами. Количество возможных ребер в графе квадратично
зависит от количества вершин (для понимания можно представить турнирную таблицу сыгранных матчей).
Дерево – это связный граф без циклов. Связность означает, что из любой вершины в любую другую существует путь по ребрам.
 Отсутствие циклов означает, что данный путь – единственный. Обход графа – это систематическое посещение всех его вершин по одному разу каждой.
Существует два вида обхода графа: 1) поиск в глубину; 2) поиск в ширину.

Поиск в ширину (BFS) идет из начальной вершины, посещает сначала все вершины находящиеся на расстоянии одного ребра от начальной,
потом посещает все вершины на расстоянии два ребра от начальной и так далее.
Алгоритм поиска в ширину является по своей природе нерекурсивным (итеративным). Для его реализации применяется структура данных очередь (FIFO).

Поиск в глубину (DFS) идет из начальной вершины, посещая еще не посещенные вершины без оглядки на удаленность от начальной вершины.
Алгоритм поиска в глубину по своей природе является рекурсивным. Для эмуляции рекурсии в итеративном варианте алгоритма применяется структура данных стек.

С формальной точки зрения можно сделать как рекурсивную, так и итеративную версию как поиска в ширину, так и поиска в глубину.
 Для обхода в ширину в обоих случаях необходима очередь. Рекурсия в рекурсивной реализации обхода в ширину всего лишь эмулирует цикл.
Для обхода в глубину существует рекурсивная реализация без стека, рекурсивная реализация со стеком и итеративная реализация со стеком.
 Итеративная реализация обхода в глубину без стека невозможна.
*/

/*
Поиск кротчайшего пути является одной из классических задач теории графов
алгоритм Дейкстры — находит кратчайший путь от одной из вершин графа до всех остальных.
                    Алгоритм работает только для графов без рёбер отрицательного веса.
алгоритм Беллмана-Форда — находит кратчайшие пути от одной вершины графа до всех остальных во взвешенном графе.
                          Вес ребер может быть отрицательным.
алгоритм Флойда-Уоршелла — находит кратчайшие пути между всеми вершинами взвешенного ориентированного графа.
алгоритм Джонсона — находит кратчайшие пути между всеми парами вершин взвешенного ориентированного графа.
алгоритм Ли (волновой алгоритм) — находит путь между вершинами графа, содержащий минимальное количество промежуточных вершин (ребер).
                                  Используется для поиска кратчайшего расстояния на карте в стратегических играх.
алгоритм поиска A* — находит маршрут с наименьшей стоимостью от одной вершины (начальной) к другой (целевой, конечной),
                                         используя алгоритм поиска по первому наилучшему совпадению на графе.
 */
/*
 Основная тема содержится в обходе,после нахождения всех возможных вариантов можно отфильтровать собранные данные
 Добавить ньюансы обхода:
 + направление ребер, направленность стоимости
 + поиск пути с минимальным количеством узлов
*/
declare(strict_types=1);

class Node{
    public string $id;
    public array $routes=[];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function addRoute(Route &$route){
       $this->routes[$route->id]=&$route;
    }
}


class Route{
    public string $id;
    public int $value;
    public Node $left;
    public Node $right;
    public bool $directionLeftToRight;

    public function __construct(string $id,int $value,Node &$left,Node &$right,bool $directionLeftToRight=null)
    {
        $this->id = $id;
        $this->value = $value;
        $this->left = &$left;
        $this->right = &$right;
        if(!is_null($directionLeftToRight)){
            $this->directionLeftToRight = $directionLeftToRight;
            // направленность стоимости возможно давить логику в методах вместо прямого доступа к value
        }
    }
}
/*
  Обойти все узлы от и до учитывая три условия:
 - не должен быть целевым узлом (нет нужды его посещать, данные его маршрута уже взяты)
 - маршрут к узлу должен позволить использовать направление перемещения
 - узел должен быть еще не посещенным данным вариантом построения маршрута
 После сбора всех возможных вариантов отфильтровать собранные данные пользовательской функцией.
  */
class Map{
    public array $routes;
    public array $nodes;
    private bool $validate=false;

    public function __construct(){
        $this->routes=[];
        $this->nodes=[];
    }

    public function addNode(Node &$node){
        $this->nodes[$node->id]=&$node;
    }

    public function addRoute(Route &$route){
        $this->validate=false;
        if(isset($this->routs[$route->id])){
            throw new RuntimeException("This rout id exist");
        }
        foreach ($this->routes as $item){
            if($item===$route){
                throw new RuntimeException("The rout exist");
            }
            if(($item->left === $route->left && $item->right === $route->right) ||
                ($item->left === $route->right && $item->right === $route->left)
            ){
                print_r([$item,$route]);
                throw new RuntimeException("rout exist");
            }
        }
         $this->routes[$route->id]=$route;
    }

    public function shortRoadId(string $nodeIdStart,string $nodeIdEnd,callable $logicResult):array {
        if(!$this->validate){
            $this->validate();
            if(!$this->validate) throw new RuntimeException("Map is not validate");
        }
        $start=null;
        $end=null;
        foreach ($this->nodes as &$node){
            if($node->id == $nodeIdStart)$start=&$node;
            if($node->id == $nodeIdEnd)$end=&$node;
        }
        if($start===$end || is_null($end) || is_null($start))
            throw new RuntimeException("Node not found");

        return $this->shortRoadNode($start,$end,$logicResult);
    }

    public function shortRoadNode(Node &$start,Node &$end,callable $logicResult):array {
        if(!$this->validate){
            $this->validate();
            if(!$this->validate) throw new RuntimeException("Map is not validate");
        }
        $result = [];
        foreach ($start->routes as &$route){
            if($route->right->id!=$start->id &&  ((isset($route->directionLeftToRight) && $route->directionLeftToRight==true)|| !isset($route->directionLeftToRight)) ){

                $idHistory = $start->id."-".$route->right->id;
                $result[$idHistory]=['count'=>$route->value,'list'=>[$start->id]];
                $this->recursiveRoute( $idHistory,$end->id,$route->right,$result);
            }elseif( ((isset($route->directionLeftToRight) && $route->directionLeftToRight==false)|| !isset($route->directionLeftToRight)) ){
                $idHistory = $start->id."-".$route->left->id;
                $result[$idHistory]=['count'=>$route->value,'list'=>[$start->id]];
                $this->recursiveRoute( $idHistory,$end->id,$route->left,$result);
            }
        }

        return $logicResult($result);
    }

    public function recursiveRoute(string $idHistory,string $endId,Node &$nextNode,array &$result){
         $isEnd = false;

         foreach ($nextNode->routes as &$route){
             // противоположный узел ребра
             if($route->left->id==$nextNode->id &&  ((isset($route->directionLeftToRight) && $route->directionLeftToRight==true)|| !isset($route->directionLeftToRight))){
                 if(array_search($route->right->id,$result[$idHistory]['list'])===false){
                     $idHistoryNew = $idHistory."-".$route->right->id;
                     $result[$idHistoryNew]=$result[$idHistory];
                     $result[$idHistoryNew]['count']+=$route->value;
                     array_push( $result[$idHistoryNew]['list'],$nextNode->id);
                     if($route->right->id != $endId ){
                         $this->recursiveRoute( $idHistoryNew,$endId,$route->right,$result);
                     }else{
                         array_push( $result[$idHistoryNew]['list'],$route->right->id);
                     }
                 }elseif($route->left->id == $endId){
                     $isEnd=true;
                     array_push( $result[$idHistory]['list'],$route->left->id);
                 }
             }elseif($route->right->id==$nextNode->id && ((isset($route->directionLeftToRight) && $route->directionLeftToRight==false)|| !isset($route->directionLeftToRight))){
                 if(array_search($route->left->id,$result[$idHistory]['list'])===false){
                     $idHistoryNew = $idHistory."-".$route->left->id;
                     $result[$idHistoryNew]=$result[$idHistory];

                     $result[$idHistoryNew]['count']+=$route->value;
                     array_push( $result[$idHistoryNew]['list'],$nextNode->id);
                     if($route->left->id != $endId){
                         $this->recursiveRoute( $idHistoryNew,$endId,$route->left,$result);
                     }else{
                         array_push( $result[$idHistoryNew]['list'],$route->left->id);
                     }
                 }elseif($route->right->id == $endId){
                     $isEnd=true;
                     array_push( $result[$idHistory]['list'],$route->right->id);
                 }
             }
         }
        if(!$isEnd)unset($result[$idHistory]);
    }


    public function validate():bool{
        $this->validate=false;
        foreach ($this->routes as $route){
            $validate = false;
            foreach ($this->routes as $item){
                if($route===$item)continue;
                if(($route->left === $item->left || $route->left === $item->right) ||
                    ($route->right === $item->left || $route->right === $item->right)){
                    $validate=true;
                }
            }
            if($validate==false){
                $this->validate=false;
                return $this->validate;
            }
        }
        $this->validate=true;
        return $this->validate;
    }
}

$nodeA = new Node("A");
$nodeB = new Node("B");
$nodeC = new Node("C");
$nodeD = new Node("D");
$nodeE = new Node("E");
$nodeF = new Node("F");
$nodeG = new Node("G");

$routAB = new Route("AB",4,$nodeA,$nodeB);
$routAC = new Route("AC",3,$nodeA,$nodeC);
$routAE = new Route("AE",7,$nodeA,$nodeE);
$routBC = new Route("BC",6,$nodeB,$nodeC);
$routBD = new Route("BD",5,$nodeB,$nodeD);
$routCD = new Route("CD",11,$nodeC,$nodeD);
$routCE = new Route("CE",8,$nodeC,$nodeE);
$routDE = new Route("DE",2,$nodeD,$nodeE);
$routDF = new Route("DF",2,$nodeD,$nodeF);
$routDG = new Route("DG",10,$nodeD,$nodeG);
$routEG = new Route("EG",5,$nodeE,$nodeG,false);// false - из левого узла запрещен переход в правый, true - из правого запрещенно в левый, null - обе стороны разрешены
$routFG = new Route("FG",3,$nodeF,$nodeG);

$nodeA->addRoute($routAB);
$nodeA->addRoute($routAC);
$nodeA->addRoute($routAE);
$nodeB->addRoute($routAB);
$nodeB->addRoute($routBC);
$nodeB->addRoute($routBD);
$nodeC->addRoute($routAC);
$nodeC->addRoute($routBC);
$nodeC->addRoute($routCD);
$nodeC->addRoute($routCE);
$nodeD->addRoute($routCD);
$nodeD->addRoute($routBD);
$nodeD->addRoute($routDE);
$nodeD->addRoute($routDF);
$nodeD->addRoute($routDG);
$nodeE->addRoute($routDE);
$nodeE->addRoute($routAE);
$nodeE->addRoute($routCE);
$nodeE->addRoute($routEG);
$nodeF->addRoute($routDF);
$nodeF->addRoute($routFG);
$nodeG->addRoute($routFG);
$nodeG->addRoute($routEG);
$nodeG->addRoute($routDG);

$map = new Map();
$map->addRoute($routAB);
$map->addRoute($routAC);
$map->addRoute($routAE);
$map->addRoute($routBC);
$map->addRoute($routBD);
$map->addRoute($routCD);
$map->addRoute($routCE);
$map->addRoute($routDE);
$map->addRoute($routDF);
$map->addRoute($routDG);
$map->addRoute($routEG);
$map->addRoute($routFG);

$map->addNode($nodeA);
$map->addNode($nodeB);
$map->addNode($nodeC);
$map->addNode($nodeD);
$map->addNode($nodeE);
$map->addNode($nodeF);
$map->addNode($nodeG);

echo sprintf("Validate %s",$map->validate()?'YES':'NO');

$logicResultMinCountRoute = function ($result){
    $minCount = null;
    foreach ($result as $key=>$value){
        if(is_null($minCount)){
            $minCount = $value['count'];
        }else{
            if($minCount>$value['count']){
                $minCount = $value['count'];
            }
        }
    }
    $buff = [];
    foreach ($result as $key=>$value){
        if($value['count']==$minCount){
            $buff[$key]=$value;
        }
    }
    return $buff;
};

$result = $map->shortRoadId("A","G",$logicResultMinCountRoute);
print_r($result);



$logicResultMinCountNode = function ($result){
    $minCount = null;
    foreach ($result as $key=>$value){
        if(is_null($minCount)){
            $minCount = count($value['list']) ;
        }else{
            if($minCount>count($value['list'])){
                $minCount = count($value['list']);
            }
        }
    }
    $buff = [];
    foreach ($result as $key=>$value){
        if(count($value['list'])==$minCount){
            $buff[$key]=$value;
        }
    }
    return $buff;
};
$result = $map->shortRoadId("A","F",$logicResultMinCountNode);
print_r($result);






