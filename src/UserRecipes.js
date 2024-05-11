import axios from 'axios';

// Array to store uploaded file IDs
let uploadedFileIds = [];

export default class UserRecipes {
	constructor() {
		this.events();
		axios.defaults.headers.common['X-WP-Nonce'] = meshijeData.nonce;
	}

	events() {
		const deleteRecipeBtns = document.querySelectorAll('.delete-recipe');
		const editRecipeBtns = document.querySelectorAll('.edit-recipe');
		const createRecipeBtns = document.querySelector('.create-recipe');
		const getImagesFiles = document.querySelector(
			'.meshije-image-upload input'
		);
		deleteRecipeBtns.forEach((btn) => {
			btn.addEventListener('click', (e) => this.deleteRecipe(e));
		});
		editRecipeBtns.forEach((btn) => {
			btn.addEventListener('click', (e) => this.editRecipe(e));
		});
		createRecipeBtns.addEventListener('click', (e) => this.createRecipe(e));
		getImagesFiles.addEventListener('change', (e) => this.fileReader(e));
	}
	// Function to show a dynamic alert
	showAlert(message, type) {
		const alertDiv = document.createElement('div');
		alertDiv.classList.add(
			'px-4',
			'py-3',
			'mb-4',
			'rounded',
			'shadow-md',
			'text-sm'
		);

		// Set alert color based on type
		switch (type) {
			case 'success':
				alertDiv.classList.add(
					'bg-green-100',
					'border',
					'border-green-400',
					'text-green-700'
				);
				break;
			case 'error':
				alertDiv.classList.add(
					'bg-red-100',
					'border',
					'border-red-400',
					'text-red-700'
				);
				break;
			case 'info':
				alertDiv.classList.add(
					'bg-blue-100',
					'border',
					'border-blue-400',
					'text-blue-700'
				);
				break;
			default:
				alertDiv.classList.add(
					'bg-gray-100',
					'border',
					'border-gray-400',
					'text-gray-700'
				);
		}

		alertDiv.innerHTML = `
			<p class="font-bold">${type.toUpperCase()}</p>
			<p>${message}</p>
		`;

		// Add the alert to the DOM, for example, to the body
		document.body.appendChild(alertDiv);

		// Automatically hide the alert after a certain time (e.g., 5 seconds)
		setTimeout(() => {
			alertDiv.remove();
		}, 5000);
	}
	// Separate function to open the modal
	openModal() {
		const modalAlert = document.querySelector('#meshije-delet-post');
		modalAlert.classList.remove('hidden');
	}

	// Separate function to close the modal
	closeModal() {
		const modalAlert = document.querySelector('#meshije-delet-post');
		modalAlert.classList.add('hidden');
	}

	// Separate function to handle the delete action
	handleDelete(recipeId) {
		axios
			.delete(
				`${meshijeData.root_url}/wp-json/wp/v2/user-recipes/${recipeId}`
			)
			.then((response) => {
				console.log(response.data); // Logging the response data
				alert('Recipe deleted successfully!');
				this.showAlert('Recipe deleted successfully!', 'success');
			})
			.catch((error) => {
				console.error(error); // Log any errors
				alert('Error deleting recipe');
			})
			.finally(() => {
				this.closeModal();
			});
	}

	// Modified deleteRecipe method to use the above functions
	deleteRecipe(e) {
		const recipeId = e.target.dataset.recipeId;
		this.openModal();

		const deleteBtn = document.getElementById('meshije-delet-btn');
		deleteBtn.addEventListener('click', () => {
			this.handleDelete(recipeId);
		});

		const cancelBtn = document.getElementById('meshije-cancel-btn');
		cancelBtn.addEventListener('click', () => {
			this.closeModal();
		});
	}

	editRecipe(e) {
		const recipeId = e.target.dataset.recipeId;

		console.log(e.target);
		const fieldToUpdate = {
			title: 'test',
			content: 'test 23',
		};
		axios
			.patch(
				`${meshijeData.root_url}/wp-json/wp/v2/user-recipes/${recipeId}`,
				fieldToUpdate
			)
			.then((response) => {
				console.log(response.data); // Logging the response data
				alert('Recipe updated successfully!');
			})
			.catch((error) => {
				console.error(error); // Log any errors
				alert('Error updating recipe');
			});
	}

	fileReader(e) {
		const files = e.target.files;
		const preview = document.getElementById('preview');

		// Remove any existing previews
		preview.innerHTML = '';

		// Loop through the files
		for (let i = 0; i < files.length; i++) {
			const file = files[i];

			// Create a FileReader to read the file
			const reader = new FileReader();
			const divWrapper = document.createElement('div');
			const cssClass =
				'overflow-hiddenrelative rounded-xl w-full min-h-[60vh] max-h-[60vh] h-[60vh] lg:h-[45vh] lg:min-h-[45vh] lg:max-h-[45vh] border-4 border-gray-200 cursor-pointer';
			divWrapper.classList = cssClass;
			// Set up the FileReader to display the preview
			reader.onload = (e) => {
				// Create an image element
				const img = document.createElement('img');
				img.src = e.target.result;
				img.style.maxWidth = '200px'; // Optional: Limit image width for preview

				// Create a paragraph element for the file name
				const fileNameHTML = document.createElement('p');
				fileNameHTML.textContent = file.name;
				fileNameHTML.classList.add('file-name');
				// Create a remove button
				const removeBtn = document.createElement('button');
				removeBtn.textContent = 'Remove';
				removeBtn.addEventListener('click', () => {
					this.removeImage(i);
					preview.removeChild(divWrapper);
				});

				// Add click event to set this image as featured
				img.addEventListener('click', () => {
					this.setFeaturedImage(img, i);
				});

				// Append the image and remove button to the preview div
				divWrapper.appendChild(img);
				divWrapper.appendChild(fileNameHTML);
				divWrapper.appendChild(removeBtn);
				preview.appendChild(divWrapper);

				// Upload the file and get its ID
				this.uploadFileAndGetId(file);
			};

			// Read the file as a data URL (for image previews)
			reader.readAsDataURL(file);
		}
	}

	removeImage(index) {
		uploadedFileIds.splice(index, 1);
	}

	setFeaturedImage(img, index) {
		const allImages = document.querySelectorAll('.featured-img');
		allImages.forEach((image) => {
			image.classList.remove('featured-img');
		});
		img.classList.add('featured-img');

		// Move the selected file ID to the beginning of the array
		const selectedFileId = uploadedFileIds.splice(index, 1)[0];
		uploadedFileIds.unshift(selectedFileId);
	}

	uploadFileAndGetId(file) {
		const formData = new FormData();
		formData.append('file', file);

		axios
			.post(`${meshijeData.root_url}/wp-json/wp/v2/media`, formData, {
				headers: {
					'Content-Type': 'multipart/form-data',
				},
			})
			.then((response) => {
				const fileId = response.data.id;
				uploadedFileIds.push(fileId);
			})
			.catch((error) => {
				console.error('Error uploading file:', error);
				alert('Error uploading file. Please try again.');
			});
	}

	createRecipe(e) {
		const featuredImageId =
			uploadedFileIds.length > 0 ? uploadedFileIds[0] : null;
		console.log(featuredImageId);
		const ourPost = {
			title: document.querySelector('.create-recipe-title').value,
			content: document.querySelector('.create-recipe-content').value,
			status: 'publish',
			featured_media: featuredImageId,
			acf: {
				preparation_time: 'test from front end',
				cooking_time: 'test from front end',
				recipes_gallery: uploadedFileIds,
			},
		};
		axios
			.post(
				`${meshijeData.root_url}/wp-json/wp/v2/user-recipes/`,
				ourPost
			)
			.then((response) => {
				document.querySelector('.create-recipe-title').value = '';
				document.querySelector('.create-recipe-content').value = '';
				// Clear the uploadedFileIds array after creating the recipe
				uploadedFileIds = [];
				// TODO: HTML To show the new recipe in dashboard

				console.log(response.data); // Logging the response data
				alert('Recipe updated successfully!');
			})
			.catch((error) => {
				console.error(error); // Log any errors
				alert('Error updating recipe');
			});
	}
}
export const recipes = new UserRecipes();
