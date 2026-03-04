 
 <html xmlns="https://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<form method="post" a enctype="multipart/form-data" action="">
    <input type="text" name="n" autocomplete="off"/>
    <input type="submit" value="submit" name="submit" >

</form>
<?
if(isset($_PSOT['submit']))
{
$name = $_PSOT['n'];
$connect = mysqli_connect("mysql","root","root","test");
mysql_set_charset('utf8');
$query = mysql_query("insert into ex(name) values('$name')");
mysql_set_charset('uft8');
$query =mysql_query("select * from ex ");
while ($ros=mysql_fetch_array($query1)) {
    $name1 = $ros['name'];
    echo "$name1";
    echo " ";
 }
}
?>
</body>
</html>