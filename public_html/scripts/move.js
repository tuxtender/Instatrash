//	move.js

var moveBtn = document.getElementById('move');
moveBtn.addEventListener('click', moveMenu);

var moveModal = document.getElementById('moveModal');

var moveHereBtn = document.getElementById('moveModal_accept');
moveHereBtn.addEventListener('click', moveFiles);
moveHereBtn.addEventListener('click', exitMoveMenu);

var moveBackBtn = document.getElementById('moveModal_back');

var menuMoveContent = document.getElementById('moveModal_currentDir_content');

var moveMenuCancel = document.getElementById('moveModal_close');
moveMenuCancel.addEventListener('click', exitMoveMenu);

var menuMoveCurrentFolder = document.getElementById('moveModal_currentDir');

function moveMenu() {
	moveModal.style.display = "flex";
	initSubFolders();
	moveBackBtn.addEventListener('click', showSubFolders);
	
}

function initSubFolders() {
	let div = document.createElement('div');
	div.folderFullName = '/';
	div.addEventListener('click', showSubFolders);
	div.dispatchEvent(new Event('click'));
	div.remove();
}

function showSubFolders(evnt) {
	let path = evnt.currentTarget.folderFullName;
	//	Clear 
	while (menuMoveContent.firstChild) {
		menuMoveContent.removeChild(menuMoveContent.firstChild);
	}
	
	moveHereBtn.destinationPath = path;
	menuMoveCurrentFolder.innerText = getFolderName(path);
	moveBackBtn.folderFullName =  getFolderPath(path);
	
	// Fetch a content of directory(var path)
	var formData = new FormData();
	formData.append('path', path); 
	
	var myInit = { 	method: 'POST',
					body: formData,  
					mode: 'cors',
					cache: 'default' };

	var dataRequest = new Request( 'src/data.php', myInit);
      
	fetch(dataRequest)				
		.then(function(response) {
			return response.json()
		})
		.then(function(json) {		
			let dirs = json['folders'];
			let files = json['files'];
		
			let foldersHeader = document.createElement('div');
			foldersHeader.setAttribute("class", "moveModal_header");
			foldersHeader.innerText = "Folders";
			menuMoveContent.append(foldersHeader);
			
			for (let dir of dirs) {
				let d = document.createElement('div');
				d.setAttribute("class", "moveModal_item");
				d.innerText = dir['folder_name'];
				d.folderFullName = dir['path'] + dir['folder_name'] + '/';
				menuMoveContent.append(d);
				d.addEventListener('click', showSubFolders);
			
			}
			
			let filesHeader = document.createElement('div');
			filesHeader.setAttribute("class", "moveModal_header");
			filesHeader.innerText = "Files";
			menuMoveContent.append(filesHeader);
			
			for (let file of files) {
				let d = document.createElement('div');
				d.setAttribute("class", "moveModal_item na");
				d.innerText = file['thumb'];
				//d.innerText = file['name'];
				menuMoveContent.append(d);
						
			}
			
		});

}


function getFolderName(str) {
	let p = str.lastIndexOf("/", str.length-2);
	let folderName = str.slice(p+1, str.length-1);
	return folderName;
}

function getFolderPath(str) {
	let pos = str.lastIndexOf("/", str.length-2);
	return  str.slice(0, pos+1) ;
}

function moveFiles(e) {
	let path = e.currentTarget.destinationPath;
	let request = {};
	let files = [];
	let folders = [];
	
	for (let frame of selected) {
		if (frame. isDirectory) {
			folders[folders.length] = frame.userData['folder_name'];
		} else {
			files[files.length] = frame.userData['thumb'];
		}
		
		frame.remove();
	}
	
	request['destination'] = path;	
	request['work_directory'] = currentDir;
	request['files'] = files;
	request['folders'] = folders;
		
	var requestJson = JSON.stringify(request);
	
	const removeRequest = new Request('src/move.php', {
		method: 'POST',
		mode: 'cors',
		cache: 'default',
		body: requestJson,
	});

	fetch(removeRequest)
		.then((response) => response.blob())
		.then((zipArchive) => {
			
	  });
	
}

function exitMoveMenu() {
	moveModal.style.display = "none";
	back_menu_btn.click();

}
