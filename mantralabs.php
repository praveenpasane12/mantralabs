	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Task</title>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<script type="text/javascript" src="papaparse.min.js"></script>
	</head>
	<style>
		fieldset {
			padding: 15px 0;
			border:none;
			text-align: center;
		}
		.container {
			background-color: #e1ebf6;
		}
		#introHeader{
			text-align: center;
			padding: 10px;
		}
		.bg{
    width: 100%;
    height:auto;
	min-height:100vh;
    background-image:url(http://i.imgur.com/w16HASj.png);
    background-size: 100% 100%;
    background-position: top center;
  }

  .content{
margin-top: 20%;
  }

  .centered {
  position: absolute;
  top: 40%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.InputStyle{
  border-radius: 25px;
  border: solid 1px white;
 background: transparent;
 width: 300px;
    padding: 10px 20px;
}



input,input::-webkit-input-placeholder {
    font-size: 12px;
  color:white;
}

.social-btn{
 position: absolute; 
  bottom: 20px;
  left: 47%;
}

i{
 padding:5px;
  color:white;

}

input, input:focus{
  border: solid 1px white;
        outline:0; 
        -webkit-appearance:none;
        box-shadow: nones;
        -moz-box-shadow: none;
        -webkit-box-shadow: none;
}

.secondLine{
  font-weight: 350;
  font-size:15px;
  margin-bottom: 15%;
  color: white;

}

.firstLine{
font-size: 30px;
color: white;
}

@media only screen and (max-width: 600px) {
  .firstLine{
font-size: 20px;
}
}
#txtFileUpload{
	margin:10px 0;
}
	</style>
	<body>
<div class="bg text-center">
  <div class="centered">
   
     <p class="firstLine"> Upload your CSV File</p>
   
    <form>
					<input type="file" class="form-control" name="file" id="csvFileInput"/>
					<input type="submit" class="btn btn-outline-light" name="File_Upload" value="Upload" id="txtFileUpload">
				</form>
				<span class="successMsg" style="display:none;font-size:20px"></span>



    </div>
   
  </div>
	</body>
	<script>
		var firstRun = true;
		var jsonToCsvArray = [];
		function enableButton() {
			$("#txtFileUpload").removeClass('disabled');
		}

		function printStats(msg) {
			if (msg)
				console.log(msg);
			console.log("Time:", (end - start || "(Unknown; your browser does not support the Performance API)"), "ms");
			console.log("Row count:", rowCount);
			if (stepped)
				console.log("    Stepped:", stepped);
			console.log("Errors:", errorCount);
			if (errorCount)
				console.log("First error:", firstError);
		}

		function completeFn(results) {
			end = now();

			if (results && results.errors) {
				if (results.errors) {
					errorCount = results.errors.length;
					firstError = results.errors[0];
				}
				if (results.data && results.data.length > 0)
					rowCount = results.data.length;
			}

			printStats("Parse complete");
			var data={results:results};
			$.ajax({
				url: "Insert_update_data.php",
				data: JSON.stringify(data),
				dataType: 'JSON',
				type: "POST",
				cache: false,
				success: function(response) {
					if(response.result == "Successfully Updated") {
						$('.successMsg').html(response.result);
						$('.successMsg').css("color","greenyellow");

						$('.successMsg').show();
					} else {
						$('.successMsg').html("Something went wrong, please try again");
						$('.successMsg').show();
						$('.successMsg').css("color","red");
					}
				},
				error:function(err){
					console.log("Error"+err);
				}
			});

			setTimeout(enableButton, 100);
		}

		function errorFn(err, file) {
			end = now();
			console.log("ERROR:", err, file);
			enableButton();
		}
		function now() {
			return typeof window.performance !== 'undefined' ?
			window.performance.now() :
			0;
		}
		$("#txtFileUpload").click(function(){
			event.preventDefault();
			if ($(this).hasClass('disabled'))
				return;

			stepped = 0;
			rowCount = 0;
			errorCount = 0;
			firstError = undefined;

			$(this).addClass('disabled');

			if (!firstRun){}
				else{
					firstRun = false;
				}

				if (!$('#csvFileInput')[0].files.length) {
					alert("Please choose a CSV file to upload");
					return enableButton();
				}
				$("#csvFileInput").parse({
					config: {
delimiter: "", // auto-detect
newline: "", // auto-detect
quoteChar: '"',
header: true,
complete: completeFn,
error: errorFn,
skipEmptyLines: true
},
before: function(file, inputElem) {
	start = now();
},
error: function(err, file) {
	console.log("ERROR:", err, file);
	firstError = firstError || err;
	errorCount++;
},
complete: function() {
	end = now();
	printStats("Done with all files");
}
});
			});

		</script>
		</html>