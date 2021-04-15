const changeEvent = e => {
  e.preventDefault();

  if (e.target.getAttribute('type') === 'file') {
    const fileReader = new FileReader();

    fileReader.onload = e => {
      document.querySelector('.user_img').src = e.target.result;
    }

    fileReader.readAsDataURL(e.target.files[0]);
  }

}

export default changeEvent;
