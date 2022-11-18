const mask = (m, t, e, c) => {
  let cursor = t.selectionStart;
  let text = t.value;
  text = text.replace(/\D/g, '');
  let l = text.length;
  let lm = m.length;
  if (window.event) {
    id = e.keyCode;
  } else if (e.which) {
    id = e.which;
  }
  fixedCursor = false;
  if (cursor < l) fixedCursor = true;
  let free = false;
  if (id == 16 || id == 19 || (id >= 33 && id <= 40)) free = true;
  ii = 0;
  mm = 0;
  if (!free) {
    if (id != 8) {
      t.value = "";
      j = 0;
      for (i = 0; i < lm; i++) {
        if (m.substr(i, 1) == "#") {
          t.value += text.substr(j, 1);
          j++;
        } else if (m.substr(i, 1) != "#") {
          t.value += m.substr(i, 1);
        }
        if (id != 8 && !fixedCursor) cursor++;
        if ((j) == l + 1) break;

      }
    }
  }
  if (fixedCursor && !free) cursor--;
  t.setSelectionRange(cursor, cursor);
}