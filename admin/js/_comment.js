/*global dotclear, jsToolBar */
'use strict';

window.addEventListener('load', () => {
  // DOM ready and content loaded

  if (typeof jsToolBar === 'function') {
    const tbComment = new jsToolBar(document.getElementById('comment_content'));
    tbComment.draw('xhtml');
  }

  // Confirm backup deletion
  document.querySelector('#comment-form input[name="delete"]').addEventListener('click', (event) => {
    if (!window.confirm(dotclear.msg.confirm_delete_comment)) {
      event.preventDefault();
      return false;
    }
    return true;
  });
});
