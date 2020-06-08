//	upload.js

var upload_menu_btn = document.getElementById('upload'); 
upload_menu_btn.addEventListener('click',menu1PopUp);
let inputFile = document.getElementById('file_input');

function menu1PopUp() {
	popup_menu.style.display = "none";
			
	let counter = document.getElementById('counter');
	
	while (counter.firstChild) {
		counter.removeChild(counter.firstChild);
	}
	let span = document.createElement('span');
	counter.appendChild(span);
	
	inputFile.span = span;
	inputFile.value = null;
	inputFile.click();
	
	let abort_menu_btn = document.getElementById('abort');
	abort_menu_btn.addEventListener('click', abort);
	function abort() {
		exit();
	}
		
	back_menu_btn.addEventListener('click', exit);
	function exit() {
		counter.classList.remove("longFileName");
		accept_menu_btn.style.display = "none";
		span.remove();
		showMenu0();
		//	Reset a abort and a back 
		abort_menu_btn.removeEventListener('click',abort);
		this.removeEventListener('click',exit);
	}	
	
	
	let accept_menu_btn = document.getElementById('accept');
	accept_menu_btn.style.display = "inline-block";
	accept_menu_btn.addEventListener('click', function () {
		for (let file of inputFile.files) {
		
			sendFile(file);
		}
		exit();
	});
}
  
inputFile.addEventListener('change', function(e) {
	popup_menu.style.display = "block";
	let span = this.span;
	
    var fileName = '';
    
    if( this.files && this.files.length > 1 ) {
		fileName = ( this.getAttribute('data-multiple-caption') || '').replace( '{count}', this.files.length );
		popup_menu.children.counter.classList.remove("longFileName");
				
    } else {
        fileName = e.target.value.split( '\\' ).pop();
        popup_menu.children.counter.classList += "longFileName";
        
	}
    if( fileName )
        span.innerHTML = fileName;
    else
        span.innerHTML = "";
  
});

 
function sendFile(file) {

  	var formData = new FormData();
	formData.set('file', file);
	
	formData.set('work_directory', currentDir);
	
	var init = {method: 'POST',
				body: formData,  
				mode: 'cors',
				cache: 'default' };

	var dataUploadRequest = new Request( 'src/upload.php', init);
	
	fetch(dataUploadRequest).then(function(response) {
		return response.json()
	}).then(function(json) {
	
		showImage(json);
		
	});
	
};
