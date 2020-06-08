//	download.js

let downloadButton = document.getElementById('download');
downloadButton.addEventListener('click', downloadZip);


function downloadZip() {
	
	var tmpDownloadLink = document.createElement("a");
	tmpDownloadLink.style.display = 'none';
	document.body.appendChild(tmpDownloadLink);
	
	let request = {};
	let files = [];
	let folders = [];

		
	for (let frame of selected) {
		if (frame. isDirectory) {
			folders[folders.length] = frame.userData['folder_name'];
		} else {
			files[files.length] = frame.userData['thumb'];
		}
		
	}
	
	if (files.length == 0 && folders.length == 0)
			return;
			
	request['work_directory'] = currentDir;
	request['files'] = files;
	request['folders'] = folders;
	
		
	var requestJson = JSON.stringify(request);
	
	const downloadRequest = new Request('src/download.php', {
		method: 'POST',
		mode: 'cors',
		cache: 'default',
		body: requestJson,
	});

	fetch(downloadRequest)
		.then((response) => response.blob())
		.then((zipArchive) => {
			let link = URL.createObjectURL(zipArchive);
			
			tmpDownloadLink.setAttribute( 'href', link );
			let archieveName = link.slice(-12)+".zip";
			tmpDownloadLink.setAttribute( 'download', archieveName );
			tmpDownloadLink.click();
			//	Free memory after 10 mins
			setTimeout(function() {
				URL.revokeObjectURL(tmpDownloadLink.href);
				tmpDownloadLink.remove(); 
			}, 600000);
						
			back_menu_btn.click();
			
	  });
	
}

