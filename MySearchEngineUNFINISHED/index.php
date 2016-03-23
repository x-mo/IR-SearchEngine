<html>
<head>
	<title>X Search Engine</title>
	<link rel="stylesheet" href="style.css">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>
<body>

<div align="center" id="content">
<form action="index.php" method="POST">
<input type="text" name="query">
<input type="submit" name="submit" value="Search">
</form>
<br>
<?php
function fileread(){
global $File;
global $N;
$i='A';
$filename="Random Text Files/$i.txt";
while(file_exists($filename))
{
$j=ord($i)-64;
$File[$j]=file_get_contents($filename);
$i++;
$filename="Random Text Files/$i.txt";
}
$N=$j;
}

function calcTF(){
global $File,$TF;
$i=1;
while(array_key_exists($i,$File)){
$TF[$i]=array(
"A"=>substr_count ($File[$i],'A'),
"B"=>substr_count ($File[$i],'B'),
"C"=>substr_count ($File[$i],'C'),
"D"=>substr_count ($File[$i],'D')
);
$max=max($TF[$i]);
$TF[$i]['A']=$TF[$i]['A']/$max;
$TF[$i]['B']=$TF[$i]['B']/$max;
$TF[$i]['C']=$TF[$i]['C']/$max;
$TF[$i]['D']=$TF[$i]['D']/$max;
$i++;
}
}

function calcIDF(){
global $N,$TF,$IDF,$DF;
$DF=array("A"=>0,"B"=>0,"C"=>0,"D"=>0);
for($i=1;$i<=$N;$i++){
if($TF[$i]['A']!=0) $DF['A']++;
if($TF[$i]['B']!=0) $DF['B']++;
if($TF[$i]['C']!=0) $DF['C']++;
if($TF[$i]['D']!=0) $DF['D']++;
}
if($DF['A']!=0)$IDF['A']=log($N/$DF['A']);else $IDF['A']=0;			//div. by zero if a term doesn't exist in any docs
if($DF['B']!=0)$IDF['B']=log($N/$DF['B']);else $IDF['B']=0;
if($DF['C']!=0)$IDF['C']=log($N/$DF['C']);else $IDF['C']=0;
if($DF['D']!=0)$IDF['D']=log($N/$DF['D']);else $IDF['D']=0;
}

function calcW(){
global $TF,$IDF,$W,$N;
for($i=1;$i<=$N;$i++){
$W[$i]['A']=$TF[$i]['A']*$IDF['A'];
$W[$i]['B']=$TF[$i]['B']*$IDF['B'];
$W[$i]['C']=$TF[$i]['C']*$IDF['C'];
$W[$i]['D']=$TF[$i]['D']*$IDF['D'];
}
}

function calcQTF(){
global $QTF,$QueryLine,$Flag;
$Flag=0;
$QueryLine=$_POST["query"];
if($QueryLine==""){$Flag=1;return;}
$QueryChars= str_split($QueryLine);
$QTF=array("A"=>0,"B"=>0,"C"=>0,"D"=>0);
for($i=0;$i<strlen($QueryLine);$i++){
if($QueryChars[$i]=='A')
$QTF['A']++;
if($QueryChars[$i]=='B')
$QTF['B']++;
if($QueryChars[$i]=='C')
$QTF['C']++;
if($QueryChars[$i]=='D')
$QTF['D']++;
}
if($QTF['A']==0 and $QTF['B']==0 and $QTF['C']==0 and $QTF['D']==0){$Flag=1;return;}
$max=max($QTF);
if($max!=0){
$QTF['A']=$QTF['A']/$max;
$QTF['B']=$QTF['B']/$max;
$QTF['C']=$QTF['C']/$max;
$QTF['D']=$QTF['D']/$max;
}
}

function calcQIDF(){
global $N,$QTF,$QIDF,$DF;
$QDF=array("A"=>$DF['A'],"B"=>$DF['B'],"C"=>$DF['C'],"D"=>$DF['D']);
if($QTF['A']!=0) $QDF['A']++;
if($QTF['B']!=0) $QDF['B']++;
if($QTF['C']!=0) $QDF['C']++;
if($QTF['D']!=0) $QDF['D']++;

if($QDF['A']!=0)$QIDF['A']=log(($N+1)/$QDF['A']);else $QIDF['A']=0;
if($QDF['B']!=0)$QIDF['B']=log(($N+1)/$QDF['B']);else $QIDF['B']=0;
if($QDF['C']!=0)$QIDF['C']=log(($N+1)/$QDF['C']);else $QIDF['C']=0;
if($QDF['D']!=0)$QIDF['D']=log(($N+1)/$QDF['D']);else $QIDF['D']=0;
}

function calcQW(){
global $QW,$QTF,$QIDF;
$QW['A']=$QTF['A']*$QIDF['A'];
$QW['B']=$QTF['B']*$QIDF['B'];
$QW['C']=$QTF['C']*$QIDF['C'];
$QW['D']=$QTF['D']*$QIDF['D'];
}

function calcCosSim(){
global $W,$QW,$N,$Result;
$QW['A']+=0.00000000000001;
$QW['B']+=0.00000000000001;
$QW['C']+=0.00000000000001;
$QW['D']+=0.00000000000001;
for($i=1;$i<=$N;$i++){
$W[$i]['A']+=0.00000000000001;
$W[$i]['B']+=0.00000000000001;
$W[$i]['C']+=0.00000000000001;
$W[$i]['D']+=0.00000000000001;
//if(sqrt((pow($W[$i]['A'],2)+pow($W[$i]['B'],2)+pow($W[$i]['C'],2)+pow($W[$i]['D'],2))*(pow($QW['A'],2)+pow($QW['B'],2)+pow($QW['C'],2)+pow($QW['D'],2)))==0){$Result[$i]=-1;continue;} Skip div. by Zero
$Result[$i]=($W[$i]['A']*$QW['A']+$W[$i]['B']*$QW['B']+$W[$i]['C']*$QW['C']+$W[$i]['D']*$QW['D'])/sqrt((pow($W[$i]['A'],2)+pow($W[$i]['B'],2)+pow($W[$i]['C'],2)+pow($W[$i]['D'],2))*(pow($QW['A'],2)+pow($QW['B'],2)+pow($QW['C'],2)+pow($QW['D'],2)));
}
}

function printOrderRes(){
global $Result,$N;
$check=1;
for($j=1;$j<=$N;$j++){
$max=max($Result);
$i=array_search($max,$Result);
$Result[$i]=-1;
if($max<=0.001)continue;
//echo"Doc: ".$i."<br>";
$D=chr($i+64);
echo"<a href=\"Random Text Files/$D.txt\">Doc: ".$D."</a><br>";
$check=0;
}
if($check==1)echo"No Results Found.<br>";
}

/*START NEW CODE*/
function MxMul($p, $q) {
for($i=0;$i<4;$i++)
    {
        for($j=0;$j<1;$j++)
        {
            $mx[$i][$j]=1;
        }
    }
for($c=0;$c<4;$c++)
        {
            for($d=0;$d<1;$d++)
            {
                for($p=0;$p<4;$p++)
                {
                    $mx[$c][$d]+=$p[$c][$p]*$q[$p][$d];
                }
            }
        }
    return $mx;
}
function matrixtransp($m){
	$r=count($m);
	$c=count($m[0]);
	$mt=array();
	for($i=0;$i< $r;$i++){
		for($j=0;$j<$c;$j++){
			$mt[$j][$i]=$m[$i][$j];
		}
	}
	return($mt);
}
function createAMx(){
global $File;
global $N;
global $A;
$a=array(1=>0,2=>0,3=>0,4=>0);
$b=array(1=>0,2=>0,3=>0,4=>0);
$c=array(1=>0,2=>0,3=>0,4=>0);
$d=array(1=>0,2=>0,3=>0,4=>0);
for($i=1;$i<=$N;$i++)
{
	if(substr_count ($File[$i],"A")!=0)$a[$i]=1;
	if(substr_count ($File[$i],"B")!=0)$b[$i]=1;
	if(substr_count ($File[$i],"C")!=0)$c[$i]=1;
	if(substr_count ($File[$i],"D")!=0)$d[$i]=1;
}
$A=array(
		array(0,$b[1],$c[1],$d[1]),
		array($a[2],0,$c[2],$d[2]),
		array($a[3],$b[3],0,$d[3]),
		array($a[4],$b[4],$c[4],0),);
}
function caclcScore(){
global $A;
$hA = array(1,1,1,1);
$aA = array(1,1,1,1);
$hB = array(1,1,1,1);
$aB = array(1,1,1,1);
$hC = array(1,1,1,1);
$aC = array(1,1,1,1);
$hD = array(1,1,1,1);
$aD = array(1,1,1,1);
for($i=0;$i<4;$i++)
    {
        for($j=0;$j<1;$j++)
        {
            $AT[$i][$j]=0;
        }
    };
$AT=matrixtransp($A);
for($k=0;$k<5;$k++)
{
$aA=MxMul($A,$A);
}
print_r($aA);
}
/*END NEW CODE*/


fileread();
calcTF();
calcIDF();
calcW();

if(isset($_POST['submit'])){
calcQTF();
echo"Query: \"$QueryLine\"<br>";
if($Flag==1){if($QueryLine=="")echo"Enter a Query.";else echo"No Results Found.<br>";}
else{
calcQIDF();
calcQW();
calcCosSim();
/*START NEW CODE*/
createAMx();
caclcScore();
/*END NEW CODE*/
printOrderRes();
}
}
?>
</div>

</body>
</html>