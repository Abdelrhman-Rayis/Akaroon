<!DOCTYPE html>
<html>
<head>
<style>
ul {
  list-style-type: none;
  margin: 10px;
  padding: 0;
    border: 1px solid #555;
  width: 500px;
  background-color: #f1f1f1;
}

li a {
  display: block;
  color: #000;
  padding: 8px 16px;
  text-decoration: none;
  text-align: center;
  border-bottom: 1px solid #555;
    font-size: 30px;
}

li a.active {
  background-color: #5a68ad;
  color: white;
}

li a:hover:not(.active) {
  background-color: #555;
  color: white;
}
li:last-child {
  border-bottom: none;
}
</style>
</head>
<body>

<h2>Vertical Navigation Bar</h2>
<p>In this example, we create an "active" class with a green background color and a white text. The class is added to the "Home" link.</p>

<ul>
  <li><a class="active" href="#home">Categories</a></li>
  <li><a href="#Education">Education</a></li>
  <li><a href="#Organizations">Organizations</a></li>
  <li><a href="#Philosophy">Philosophy</a></li>
  <li><a href="#Politics">Politics</a></li>
  <li><a href="#Society">Society</a></li>
  <li><a href="#TASEEL">TASEEL</a></li>
  <li><a href="#The-State">TheState</a></li>

</ul>

</body>
</html>
