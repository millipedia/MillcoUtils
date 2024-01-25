<style nonce="<?=$page->nonce?>">

.mu_edit_bar {
  position: fixed;
  top: 4px;
  bottom: auto;
  left: calc(50vw - 100px);
  display: flex;
  gap:0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;;
  background-color: #222;
  padding: 4px 8px;
  color: white;
  box-shadow: 2px 2px 4px #666;
  z-index:999;
  cursor:move;

}

.mu_edit_bar a{
  display: flex;
  flex-direction: column;
  text-decoration: none;
  font-size: 11px;
  align-items: center;
  justify-content: center;
  max-width:fit-content;
  cursor:pointer;
}

.mu_edit_bar a:hover{
  color:blueviolet;
  color:var(--accent);
}

.mu_edit_bar a svg{
  width:100%;
  max-width: 20px;
  height:auto;
}

</style>


<div id="mu_edit_bar" class="mu_edit_bar">


<a href="<?=$page->editURL?>">
<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
      <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
        <path d="M7 7H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1"></path>
        <path d="M20.385 6.585a2.1 2.1 0 0 0-2.97-2.97L9 12v3h3l8.385-8.415zM16 5l3 3"></path>
      </g>
    </svg> Edit</a>

<a href="<?=$urls->admin?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
      <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
        <rect width="6" height="6" x="3" y="15" rx="2"></rect>
        <rect width="6" height="6" x="15" y="15" rx="2"></rect>
        <rect width="6" height="6" x="9" y="3" rx="2"></rect>
        <path d="M6 15v-1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1m-6-6v3"></path>
      </g>
    </svg> Pages
  </a>

  <a href="<?= $pages->get(2)->url ?>setup/template/edit?id=<?= $page->template->id ?>">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M290.8 48.6l78.4 29.7L288 109.5 206.8 78.3l78.4-29.7c1.8-.7 3.8-.7 5.7 0zM136 92.5V204.7c-1.3 .4-2.6 .8-3.9 1.3l-96 36.4C14.4 250.6 0 271.5 0 294.7V413.9c0 22.2 13.1 42.3 33.5 51.3l96 42.2c14.4 6.3 30.7 6.3 45.1 0L288 457.5l113.5 49.9c14.4 6.3 30.7 6.3 45.1 0l96-42.2c20.3-8.9 33.5-29.1 33.5-51.3V294.7c0-23.3-14.4-44.1-36.1-52.4l-96-36.4c-1.3-.5-2.6-.9-3.9-1.3V92.5c0-23.3-14.4-44.1-36.1-52.4l-96-36.4c-12.8-4.8-26.9-4.8-39.7 0l-96 36.4C150.4 48.4 136 69.3 136 92.5zM392 210.6l-82.4 31.2V152.6L392 121v89.6zM154.8 250.9l78.4 29.7L152 311.7 70.8 280.6l78.4-29.7c1.8-.7 3.8-.7 5.7 0zm18.8 204.4V354.8L256 323.2v95.9l-82.4 36.2zM421.2 250.9c1.8-.7 3.8-.7 5.7 0l78.4 29.7L424 311.7l-81.2-31.1 78.4-29.7zM523.2 421.2l-77.6 34.1V354.8L528 323.2v90.7c0 3.2-1.9 6-4.8 7.3z"/></svg>
        [<?=$page->template->name?>]      </a>

  </div>

  <script nonce="<?=$page->nonce?>">

  const edit_bar=document.getElementById("mu_edit_bar");

// Make the edit bar draggable:
// This is pretty much just from.
// https://www.w3schools.com/howto/howto_js_draggable.asp
dragElement(edit_bar);

function dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  
  elmnt.onmousedown = dragMouseDown;

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    // stop moving when mouse button is released:
    document.onmouseup = null;
    document.onmousemove = null;
  }
}

</script>