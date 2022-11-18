export default (elem, properties, children = []) => {
  const element = document.createElement(elem);

  if(properties.innerText) {
    element.innerText = properties.innerText;

    delete properties.innerText;
  }

  if(properties.innerHTML) {
    element.innerHTML = properties.innerHTML;

    delete properties.innerHTML;
  }

  Object.keys(properties).forEach((propertie, idx) => {
    element.setAttribute(propertie, Object.values(properties)[idx]);
  });

  children.length > 0 && children.map((child) => {
    if(typeof child === 'string') {
      element.innerHTML += child;
    } else {
      element.appendChild(child);
    }
  });

  return element;
}