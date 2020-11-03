<?php

include("model3d.php");
$model3d = new Model3D();
$model3d->drawGridXY(80, 80);
gen($model3d);
$model3d->drawOrigin();


header("Content-type: image/png");
imagepng($model3d->canvas);
imagedestroy($imgcanvas->canvas);

function gen($model)
    {
    // generate nodes and lines for a truss3d
        $node = array();
        $line = array();
        $n = 4; // no of in-scribed polygon sides
        $m = 2; // no of storey
        $dh =200; // height of each storey
        $r = 120; // radius of polygon
        $dr = -20; // reduce radius per storey
        $dang = 360/$n;
        $startang = $dang/2;
        
        for ($j=0;$j<$m+1;$j++){
            for($i=0;$i<$n;$i++){
                $ang = $startang+$i*$dang;
                $ang = $ang/180.0*pi();
                $x = ($r+$dr*$j)*cos($ang);
                $y = ($r+$dr*$j)*sin($ang);
                $z = $j*$dh;
                
                $node[] = array(
                    "x" => $x,
                    "y" => $y,
                    "z" => $z,
                    "dof" => "000",
                    "dx" => 0.00,
                    "dy" => 0.00,
                    "dz" => 0.0
                );
                
                $i1 = ($i+$n*$j)%$n;
                $j1 = ($i+$n*$j+1)%$n;
                if ($j < $m){
                $line[] = array(
                    "I" => $n*($j+1)+$i1,
                    "J" => $n*($j+1)+$j1,
                    "Mat" => 0,
                    "Sec" => 0
                );
                $line[] = array(
                    "I" => $n*$j+$i1,
                    "J" => $n*($j+1)+$i1,
                    "Mat" => 0,
                    "Sec" => 0
                );
                $line[] = array(
                    "I" => $n*$j+$i1,
                    "J" => $n*($j+1)+$j1,
                    "Mat" => 0,
                    "Sec" => 0
                );
                }
                
            }
        }
        
        $model->drawNodesLine($node, $line);
}
?>