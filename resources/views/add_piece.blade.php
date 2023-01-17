


<!DOCTYPE html>

<html>
		<head>
			<title>
				Request and response
			</title>
			
			<script>
			function suggest(str){
				if(str.length==0){
					document.getElementById('demo').innerHTML = ' ';
			
			}else{
				//AJAX Request
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						//var myObj = JSON.parse(this.responseText);
						document.getElementById('demo').innerHTML = this.responseText;
						
					}
					
				};
				xmlhttp.open('GET', 'Controllers/suggestion.php?q='+str, true);
				xmlhttp.send();
				
			}
			}
			</script>
		</head>
		
		<body>
			<form>
				<label for="search">
					Search
				</label>
				
				<input type="text" name = "search" id="searh" onkeyup="suggest(this.value)">
				<h4>suggestion</h4>
				<div id="demo">
					
				</div>
			</form>
		</body>

</html>