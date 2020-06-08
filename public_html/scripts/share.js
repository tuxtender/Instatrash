//	share.js

/*	In develop	*/

//let shareButton = document.getElementById('share');
//shareButton.addEventListener('click', share);


function share() {
	
	let files = [];
	
	for (let frame of selected) {
		files[files.length] = frame.userData['thumb'];
			
	}
	
	var filesJson = JSON.stringify(files);

	var init = {method: 'POST',
				body: filesJson,  
				mode: 'cors',
				cache: 'default' };

	var shareRequest = new Request( 'src/share.php', init);
	
	fetch(shareRequest).then(function(response) {
		return response.json()
	}).then(function(json) {
	
		showShareLink(json);
		
	});
	
}

function showShareLink(str) {
	alert(str);
};
