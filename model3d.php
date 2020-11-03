<?php
/******
 * Model3DClass
 *
 * [Model3D] class create 3 dimensional perspective view by projecting 3d coordinate system onto 2d canvas.
 * Current model provide 2 modes, z axis vertical and y axis vertical.
 * This simplest first version implement only 3 dimensional line drawing and wire frame that include 3d nodes and connected lines.
 * 3D object is centeted at PROJECTION_CENTER center point on the canvas view. View rotation is defined with $ROT about vertical axis.
 * Coordinate system obey right-hand-rule.
 * 
 * 
 * This class is free for the educational use as long as maintain this header together with this class.
 * Author: Win Aung Cho
 * Contact winaungcho@gmail.com
 * version 1.0
 * Date: 4-11-2020
 *
 ******/
class Model3D{

    var $width = 800;
    var $height = 1000;
    var $PROJECTION_CENTER_X;
    var $PROJECTION_CENTER_Y;
    var $PROJECTION_OFFSET;
    var $FIELD_OF_VIEW;
    var $ROT = 1.2;
    var $ROTCENTER = 0;
    var $zvertical = true;
    var $canvas=null;
    var $defcolor=0;
    var $deflinew=1;
    
    function __construct(){
    	$this->PROJECTION_CENTER_X = $this->width / 2;
    	$this->PROJECTION_CENTER_Y = $this->height / 3;
    	$this->PROJECTION_OFFSET = -300;
    	$this->FIELD_OF_VIEW = $this->width * 0.8;
        $this->canvas = imagecreatetruecolor($this->width, $this->height);
        $this->defcolor = imagecolorallocate($this->canvas, 255, 255, 255);
        
        define(BLACK, imagecolorallocate($img, 0, 0, 0));
        define(WHITE, imagecolorallocate($this->canvas, 255, 255, 255));
        define(RED, imagecolorallocate($this->canvas, 255, 0, 0));
        define(GREEN, imagecolorallocate($this->canvas, 0, 255, 0));
        define(BLUE, imagecolorallocate($this->canvas, 0, 200, 250));
        define(ORANGE, imagecolorallocate($this->canvas, 255, 200, 0));
        define(BROWN, imagecolorallocate($this->canvas, 220, 110, 0));
        
    }
    
    function init(){
        $this->defcolor = imagecolorallocate($this->canvas, 255, 255, 255);
    }
    
    function project2($x, $y, $z){
        if ($this->zvertical){
            $sizeProjection = $this->FIELD_OF_VIEW / ($this->FIELD_OF_VIEW + $y);
            $yProject = -(($z-$this->PROJECTION_OFFSET) * $sizeProjection) + $this->PROJECTION_CENTER_Y;
        } else {
            $sizeProjection = $this->FIELD_OF_VIEW / 
            ($this->FIELD_OF_VIEW + $z);
            $yProject = -(($y-$this->PROJECTION_OFFSET) * $sizeProjection) + $this->PROJECTION_CENTER_Y;
        }
        $xProject = ($x * $sizeProjection) + $this->PROJECTION_CENTER_X;
        return array(
            "size" => $sizeProjection,
            "x" => $xProject,
            "y" => $yProject
        );
    }
    function project($x, $y, $z){
        $sin = sin($this->ROT);
        $cos = cos($this->ROT);
        
        if ($this->zvertical){
            $rotX = $cos * $x + $sin * (($y) - $this-> ROTCENTER);
            $rotY = -$sin * $x + $cos * (($y) - $this-> ROTCENTER) + $this-> ROTCENTER;
            $sizeProjection = $this-> FIELD_OF_VIEW / ($this-> FIELD_OF_VIEW + $rotY);
            $yProject = -(($z-$this->PROJECTION_OFFSET) * $sizeProjection) + $this-> PROJECTION_CENTER_Y;
            $xProject = ($rotX * $sizeProjection) + $this-> PROJECTION_CENTER_X;
        } else {
            $rotX = $cos * $x + $sin * (($z) - $this-> ROTCENTER);
            $rotZ = -$sin * $x + $cos * (($z) - $this-> ROTCENTER) + $this-> ROTCENTER;
            $sizeProjection = $this-> FIELD_OF_VIEW / ($this-> FIELD_OF_VIEW - $rotZ);
            $xProject = ($rotX * $sizeProjection) + $this-> PROJECTION_CENTER_X;
            $yProject = -(($y-$this->PROJECTION_OFFSET) * $sizeProjection) + $this-> PROJECTION_CENTER_Y;
        }
        
        return array(
            "size" => $sizeProjection,
            "x" => $xProject,
            "y" => $yProject
        );
    }
    function drawLine($x1, $y1, $z1, $x2, $y2, $z2){
        $v1 = $this->project($x1, $y1, $z1);
        $v2 = $this->project($x2, $y2, $z2);
        imageline($this->canvas, $v1['x'], $v1['y'], $v2['x'], $v2['y'], $this->defcolor);
    }
    
    function drawGridXY($dx, $dy){
        imagesetthickness($this->canvas, $this->deflinew);
        $this->defcolor = imagecolorallocate($this->canvas, 180, 180, 180);
        for ($i=0;$i<11;$i++){
            $this->drawLine(-$dx*5+$i*$dx, -$dy*5, 0, -$dx*5+$i*$dx, $dy*5, 0);
        }
        for ($j=0;$j<11;$j++){
            $this->drawLine(-$dx*5, -$dy*5+$j*$dy, 0, $dx*5, -$dy*5+$j*$dy, 0);
        }
    }
    function drawGridXZ($dx, $dy){
        imagesetthickness($this->canvas, $this->deflinew);
        $this->defcolor = imagecolorallocate($this->canvas, 180, 180, 180);
        for ($i=0;$i<11;$i++){
            $this->drawLine(-$dx*5+$i*$dx, 0, -$dy*5, -$dx*5+$i*$dx, 0, $dy*5);
        }
        for ($j=0;$j<11;$j++){
            $this->drawLine(-$dx*5, 0, -$dy*5+$j*$dy, $dx*5, 0, -$dy*5+$j*$dy);
        }
    }
    function drawOrigin(){
        imagesetthickness($this->canvas, 5);
        $this->defcolor = RED;
        $this->drawLine(0, 0, 0, 100, 0, 0);
        $this->defcolor = GREEN;
        $this->drawLine(0, 0, 0, 0, 100, 0);
        $this->defcolor = BLUE;
        $this->drawLine(0, 0, 0, 0, 0, 100);
        //$this->defcolor = ORANGE;
        //$this->drawLine(160, 160, 0, 160, 160, 300);
    }
    function drawNodesLine($N, $L){
        $nc = count($N);
        $lc = count($L);
        $this->defcolor = GREEN;
        for ($i=0;$i<$lc;$i++){
            $n1 = $N[$L[$i]['I']];
            $n2 = $N[$L[$i]['J']];
            $this->drawLine($n1['x'], $n1['y'], $n1['z'], $n2['x'], $n2['y'], $n2['z']);
        }
    }
}
function test($m)
    {
        $node = array();
        $line = array();
        $node[] = array(
            "x" => 0.0,
            "y" => 0.0,
            "z" => 0.0,
            "dof" => "000",
            "dx" => 0.00,
            "dy" => 0.00,
            "dz" => 0.0
        );
        $node[] = array(
            "x" => 240.0,
            "y" => 0.0,
            "z" => 0.0,
            "dof" => "100",
            "dx" => 0.0,
            "dy" => 0.0,
            "dz" => 0.0
        );
        $node[] = array(
            "x" => 120.0,
            "y" => 207.8,
            "z" => 0.0,
            "dof" => "110",
            "dx" => 0.00,
            "dy" => 0.0,
            "dz" => 0.0
        );
        $node[] = array(
            "x" => 0.0,
            "y" => 0.0,
            "z" => 200.0,
            "dof" => "000",
            "dx" => 0.00,
            "dy" => 0.00,
            "dz" => 0.0
        );
        $node[] = array(
            "x" => 240.0,
            "y" => 0.0,
            "z" => 200.0,
            "dof" => "100",
            "dx" => 0.0,
            "dy" => 0.0,
            "dz" => 0.0
        );
        $node[] = array(
            "x" => 120.0,
            "y" => 207.8,
            "z" => 200.0,
            "dof" => "110",
            "dx" => 0.00,
            "dy" => 0.0,
            "dz" => 0.0
        );
        $line[] = array(
            "I" => 0,
            "J" => 1,
            "Mat" => 0,
            "Sec" => 0
        );
        $line[] = array(
            "I" => 1,
            "J" => 2,
            "Mat" => 0,
            "Sec" => 0
        );
        $line[] = array(
            "I" => 2,
            "J" => 0,
            "Mat" => 0,
            "Sec" => 0
        );
        $line[] = array(
            "I" => 3,
            "J" => 4,
            "Mat" => 0,
            "Sec" => 0
        );
        $line[] = array(
            "I" => 4,
            "J" => 5,
            "Mat" => 0,
            "Sec" => 0
        );
        $line[] = array(
            "I" => 5,
            "J" => 3,
            "Mat" => 0,
            "Sec" => 0
        );
        
        $line[] = array(
            "I" => 0,
            "J" => 3,
            "Mat" => 0,
            "Sec" => 0
        );
        $line[] = array(
            "I" => 1,
            "J" => 4,
            "Mat" => 0,
            "Sec" => 0
        );
        $line[] = array(
            "I" => 2,
            "J" => 5,
            "Mat" => 0,
            "Sec" => 0
        );
        
        $line[] = array(
            "I" => 0,
            "J" => 4,
            "Mat" => 0,
            "Sec" => 0
        );
        $line[] = array(
            "I" => 1,
            "J" => 5,
            "Mat" => 0,
            "Sec" => 0
        );
        $line[] = array(
            "I" => 2,
            "J" => 3,
            "Mat" => 0,
            "Sec" => 0
        );
        $m->drawNodesLine($node, $line);
}

?>