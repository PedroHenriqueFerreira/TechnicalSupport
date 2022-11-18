import { imgView } from '../pages/components/input.js';
import createElement from '../utils/createElement.js';

const changeEvent = async (e) => {
  const { type, className, parentElement, files } = e.target;
  const imgName = parentElement.querySelector('.input_file_visible');
  const fileReader = new FileReader();
  const loader = document.querySelector('.loader-container');

  const readFileAsDataUrl = (file, filename) => {
    return new Promise((resolve,reject) => {
  
        fileReader.onload = () => {
          let truncatedName = filename;
          if(truncatedName.length > 10) {
            truncatedName = `${truncatedName.slice(0, 10)}...`;
          }

          resolve([fileReader.result, truncatedName, filename]);
        };
  
        fileReader.onerror = () => {
            reject(fileReader);
        };
  
        fileReader.readAsDataURL(file);
    });
  }

  if (type === 'file' && !className.match('multiple')) {
    loader.appendChild(createElement('span', {}));
    const imgLoad = document.querySelectorAll('.img-load');

    fileReader.onload = (res) => {
      loader.innerHTML = '';
      imgLoad.forEach(img => {
        img.src = res.target.result;
      });

      if(files[0].name.length <= 30) {
        if(imgName) imgName.innerText = files[0].name;
      } else {
        const filename = `${files[0].name.slice(0, 27)}...`;

        if(imgName) imgName.innerText = filename;
      }
    }

    if(files[0]) {
      fileReader.readAsDataURL(files[0]);
    } else {
      loader.innerHTML = '';
      imgLoad.forEach(img => {
        img.src = res.target.result;
      });
      if(imgName) imgName.innerText = 'Escolha uma foto';
    }
  } else if(type === 'file' && className.match('multiple')) {
    loader.appendChild(createElement('span', {}));
    const imagesBlock = document.querySelector('.img-block div');

    if(files.length > 0) {
      const imgElement = await imgView();

      for(const file of files) {
        const value = await readFileAsDataUrl(file, file.name);
        imagesBlock.insertAdjacentElement('beforeend', imgElement(value[1], value[2], value[0]));

        imagesBlock.querySelectorAll('svg').forEach((svg) => {
          svg.setAttribute('class', 'delete_image');
          svg.style.display = 'block';
        });
      }

      loader.innerHTML = '';

      const filesName = [];

      imagesBlock.querySelectorAll('.img-view .label').forEach((imageView) => {
        filesName.push(imageView.getAttribute('complete'));
      });

      imgName.innerText = filesName.join(', ');

      if(!imgName.innerText) {
        imgName.innerText = 'Escolher fotos';
      }

      e.target.value = '';
    }
  } 
}

export default changeEvent;
