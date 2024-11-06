function previewImage(event) {
	const reader = new FileReader();
	reader.onload = function () {
		const imagePreview = document.getElementById("imagePreview");
		imagePreview.innerHTML =
			'<img src="' +
			reader.result +
			'" style="max-width: 100%; height: auto;" />';
	};
	reader.readAsDataURL(event.target.files[0]);
}
