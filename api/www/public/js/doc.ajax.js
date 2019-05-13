$(document).ready(function() {
  $('.form-tester').each(function(e) {
    var form = this;
    // prevent submitting form
    $(form).submit(function(e) {
      e.preventDefault();
    });
    //

    // adding field
    $(form).find('.add-input').on('click', function() {
      var name = $(form).find('.select-input-tester').val();
      if ($(form).find('input[name='+name+']').length > 0) {
        alert('Field already exists');
        return;
      }
      var input = '<div class="input-group mb-3 input '+name+'">' + '<input type="text" class="form-control mt-2" name="'+name+'" type="text" placeholder="'+ucfirst(name)+'">' + '<div class="input-group-append mt-2">' +  '<button class="btn-danger bg-danger text-white  btn input-group-text remove-input '+name+'"><small>Supprimer</small></button>' + '</div>' + '</div>';
      $(form).prepend(input);
      // onClick on delete
      var newInput = $(form).find('.remove-input.'+name);
      newInput.click(function(e) { $(form).find('.input.'+name).remove(); });

    }); // end add click

    // delete default button
    $(form).find('.default-remove-input').click(function() {
      var toRemove = $(this).parentsUntil('.input-container');
      $(toRemove).remove();
    });

    // set Button tryitout
    $(form).find('.try-it-out').on('click', function() {
        // remove succes and error response
      $('#error-response').remove();
      $('#success-response').remove();
      var method = $(form).attr('method');
      var url = $(form).attr('action');
      var regex = '#[:]+[a-zA-Z-]*#';
      var explodedSlug = url.split('/');
      for (var i = 0; i<explodedSlug.length; i++) {
        if (explodedSlug[i].indexOf(':')>-1) {
          var inputs = $(form).find('input[name="'+explodedSlug[i]+'"]');
          if (inputs.length > 0) {
            for (var j = 0; j < inputs.length; j++) {
              if (explodedSlug[i] === $(inputs[j]).attr('name')){
                var inputValue = $(inputs[j]).val();
                explodedSlug[i] = (inputValue !== '')?inputValue:'no-id-provided';
              }
            }
          }
          url = explodedSlug.join('/');
        }
      }
      var data = $(form).serialize();
      token = $('#api_doc_token').val();
      if (token !== "") {
          url += "?access_token="+token;
      }
      var contentType = method==='put'?'application/x-www-form-urlencoded; charset=UTF-8':'application/x-www-form-urlencoded; charset=UTF-8';
      $.ajax({
        method: method,
        url: url,
        data: method==='get'||method==='delete'?"":data,
        contentType: contentType,
        success: function(json, statut) {
          var title = '<div class="title mt-3">Server respond with a '+json.status.code+'.</div>';
          if (json.status.code === 200) {
            var res = JSON.stringify(json, null, 4);
            $(form).append('<div id="success-response">'+title+'<div class="tester-answer"><pre>'+res+'</pre></div></div>');
          }
        },
        error: function(json, statut, error) {
          $(form).append('<div id="error-response" class="alert alert-danger mt-3 mb-0">'+json.responseJSON.status.msg+'</div>');
        },
      });
    }); // end try it out button

    // end foreach
  });

  // $('.transform').each(function() {
  //   var card = this;
  //   $(card).addClass('show').delay(1000);
  // });

  showNext($('.transform'));

  function showNext(collection) {
    if (collection.length <= 0) { return; }
      $(collection[0]).addClass('show');
      collection = collection.slice(1);
      // showNext(collection);
      setTimeout(function() {
        showNext(collection);
      }, 60);
    }

});

// function prettyJSON(object) {
//   var jsonstr = "}\n";
//   for (var property in object) {;
//     if (typeof object[property] === 'object') {
//       jsonstr += prettyJSON(object[property]);
//     }
//     jsonstr+= '"'+propery+'": "'+object[property]'",\n';
//   }
//
//   return jsonstr;
// }

function ucfirst(str) {
  if (str.length > 0) {
    return str[0].toUpperCase() + str.substring(1);
  } else {
    return str;
  }
}
