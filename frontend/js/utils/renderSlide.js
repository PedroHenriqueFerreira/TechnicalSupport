export default () => {
  document.querySelectorAll('.radio-item img').forEach((elem) => {
    if(elem.getAttribute('slide')) {
      const slide = JSON.parse(elem.getAttribute('slide'));
  
      slide.forEach((item, idx) => {
        setTimeout(() => {
          elem.src = `/backend/uploads/${item.photo}`;
        }, idx * 2000);
      });
  
      setInterval(() => {
        slide.forEach((item, idx) => {
          setTimeout(() => {
            elem.src = `/backend/uploads/${item.photo}`;
          }, idx * 2000);
        });
      }, slide.length * 2000);
    }
  });
}
