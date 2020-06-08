//	remove.js

let delBtn = document.getElementById('delete');
delBtn.addEventListener('click', delFiles);

function delFiles() {
	
	let request = {};
	let files = [];
	let folders = [];
	
	for (let frame of selected) {
		if (frame. isDirectory) {
			folders[folders.length] = frame.userRefLink;
		} else {
			files[files.length] = frame.userData['thumb'];
		}
		frame.remove();
	}
		
	request['files'] = files;
	request['folders'] = folders;
		
	var requestJson = JSON.stringify(request);
	
	const removeRequest = new Request('src/remove.php', {
		method: 'POST',
		mode: 'cors',
		cache: 'default',
		body: requestJson,
	});

	fetch(removeRequest)
		.then((response) => response.blob())
		.then((zipArchive) => {
			back_menu_btn.click();
	  });
	
}

