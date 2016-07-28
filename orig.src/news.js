if (!window.UARKNEWS_WIDGET_LOADED) {
window.UARKNEWS_WIDGET_LOADED = true;

if (!window.console) console = {log: function() {}};  // IE8

UARKNEWS = window.UARKNEWS = window.UARKNEWS || {};

UARKNEWS.ajax_timeout_milliseconds = 6000;

UARKNEWS.wp_api_url = function(path, args) {
  args = args || {};
  for (var key in args) {
    if (args.hasOwnProperty(key)) {
      path = path.replace('$' + key, args[key]);
    }
  }
  return 'https://public-api.wordpress.com/rest/v1' + path + '?callback=?';
};

UARKNEWS.isURL = function(s) {
  // http://stackoverflow.com/questions/1701898/how-to-detect-whether-a-string-is-in-url-format-using-javascript
  var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
  return regexp.test(s);
};

UARKNEWS.photonize = function(url) {
  // this scheme doesn't handle get args, so chop them off
  url = url.split('?')[0];
  if (url.match(/i(\d+)\.wp\.com/)) {
    return url;
  } else {
    return 'http://i0.wp.com/'  + url.replace(/^https?:\/\//, '');
  }
};

UARKNEWS.append_get_args = function(url, new_args) {
  if (url.match(/\?/)) {
    return url + '&' + new_args;
  } else {
    return url + '?' + new_args;
  }
};

UARKNEWS.featured_image_url_for_post = function(post, default_image_url) {
  if (post && post.featured_image && post.featured_image.attachment_meta && post.featured_image.attachment_meta.sizes && post.featured_image.attachment_meta.sizes["large"]) {
    return post.featured_image.attachment_meta.sizes["large"].url;
  } else if (post && post.featured_image && post.featured_image.attachment_meta && post.featured_image.attachment_meta.sizes && post.featured_image.attachment_meta.sizes["large-image"]) {
    return post.featured_image.attachment_meta.sizes["large-image"].url;
  } else if (post && post.featured_image && post.featured_image.source) {
    return post.featured_image.source;
  } else if (default_image_url) {
    return default_image_url;
  } else {
    return 'http://waltoncollege.uark.edu/images/image.jpg';
  }
};

UARKNEWS.populate_widget = function($e, params) {
  (function($){
    var d = {};
    if (params.query_category) {
      d['filter[cat]'] = params.query_category;
    }
    if (params.number_of_posts_to_show) {
      d['filter[posts_per_page]'] = params.number_of_posts_to_show;
    }
    $.ajax({ dataType: 'json', timeout: UARKNEWS.ajax_timeout_milliseconds, url: params.json_api_endpoint, data: d})
      .done(function(data, textStatus, jqXHR) {

        var posts = data;

        window.params = params; //DEBUG
        window.posts = posts; //DEBUG
        // hide if no posts
        if (posts.length == 0) { $e.hide(); return; }

        // flatten categories
        $.each(posts, function(idx, post) {
          post.categories = {};
          $.each(post.terms.category, function(idx, c) {
            post.categories[c.name] = c;
          });
        });

        // posts marked Featured come before others.  Otherwise, compare on date
        posts.sort(function(a,b) {
          if ((a.categories.Featured && b.categories.Featured) || (!a.categories.Featured && !b.categories.Featured)) {
            var a_date = new Date(a.date); var b_date = new Date(b.date);
            if (a_date > b_date) { return -1; } else if (a_date < b_date) { return 1; } else { return 0; }
          } else if (a.categories.Featured) { return -1; } else { return 1; }
        });
        
        // render custom CSS
        if (params.custom_css) {
          $e.before('<style type="text/css">' + params.custom_css + '</style>');
        }
        
        $e.empty();
        $e.addClass('uark-news-embed');
        
        if (params.main_style == 'media-object') {
          var $wrapping_div = $(document.createElement('div')).addClass('col-md-12').addClass('news-item-oldschool');
          $.each(posts, function(idx, p) {
            var $post = $(document.createElement('div')).addClass('media');
            var featured_image = UARKNEWS.featured_image_url_for_post(p, "http://wordpress.uark.edu/business/files/2015/01/default-128x128.jpg");
            var featured_img_url = UARKNEWS.append_get_args(UARKNEWS.photonize(featured_image), 'resize=128,128');
            $post.append($(document.createElement('a')).addClass('pull-left').attr('href', p.link)
                          .append($(document.createElement('img')).attr('src', featured_img_url).attr('alt', p.title + ' featured image')));
            var $heading = $(document.createElement('h4')).addClass('media-heading').append(
                              $(document.createElement('a')).attr('href', p.link).append(p.title)
                            );
            $post.append($(document.createElement('div')).addClass('media-body')
                          .append($heading)
                          .append(p.excerpt)); // p.excerpt comes wrapped in <p />
            $wrapping_div.append($post);
          });
          $e.append($wrapping_div);
        } else if (params.main_style == 'enhanced') {
          var $wrapping_div = $(document.createElement('div')).addClass('row');
          $.each(posts, function(idx, p) {
            var column_classes = params.enhanced_column_class || '';
            var $post = $(document.createElement('div')).addClass('news-item ' + column_classes);
            var $post_thumbnail = $(document.createElement('div')).addClass('thumbnail');
            var featured_image = UARKNEWS.featured_image_url_for_post(p, "http://wordpress.uark.edu/business/files/2015/01/default-480x266.jpg");
            var featured_img_url = UARKNEWS.photonize(featured_image); 
            if (params.number_of_posts_to_show != '1') {
              featured_img_url = UARKNEWS.append_get_args(featured_img_url, 'resize=480,266');
            }
            $post_thumbnail.append($(document.createElement('a')).attr('href', p.link).append(
                                    $(document.createElement('img')).attr('src', featured_img_url).attr('alt', p.title + ' featured image')
                                  ));
            var $heading = $(document.createElement('h3')).append(
                              $(document.createElement('a')).attr('href', p.link).append(p.title)
                            );
            $post_thumbnail.append($(document.createElement('div')).addClass('caption')
                                    .append($heading)
                                    .append(p.excerpt) // p.excerpt comes wrapped in <p />
                                    .append($(document.createElement('p'))
                                        .append($(document.createElement('a')).addClass('btn btn-danger news-read-more').attr('href', p.link).append('Read more'))
                                    )
                                  );
            $post.append($post_thumbnail);
            $wrapping_div.append($post);
          });          
          $e.append($wrapping_div);
        }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        $e.html("Sorry, there was a problem retrieving news posts");
      });
  })(jQuery);
};

UARKNEWS.append_callback = function(url) {
  if (url.indexOf("?") > -1) {
    return url + '&callback=?';
  } else {
    return url + '?callback=?';
  }
}

UARKNEWS.bootstrap = function() {
  // load plugin to fix IE8/9 CORS AJAX
  // https://github.com/dkastner/jquery.iecors
  (function( jQuery ) {
    // Create the request object
    // (This is still attached to ajaxSettings for backward compatibility)
    jQuery.ajaxSettings.xdr = function() {
      return (window.XDomainRequest ? new window.XDomainRequest() : null);
    };

    // Determine support properties
    (function( xdr ) {
      jQuery.extend( jQuery.support, { iecors: !!xdr });
    })( jQuery.ajaxSettings.xdr() );

    // Create transport if the browser can provide an xdr
    if ( jQuery.support.iecors ) {

      jQuery.ajaxTransport(function( s ) {
        var callback,
          xdr = s.xdr();

        return {
          send: function( headers, complete ) {
            xdr.onload = function() {
              var headers = { 'Content-Type': xdr.contentType };
              complete(200, 'OK', { text: xdr.responseText }, headers);
            };
          
            // Apply custom fields if provided
            if ( s.xhrFields ) {
              xhr.onerror = s.xhrFields.error;
              xhr.ontimeout = s.xhrFields.timeout;
            }

            xdr.open( s.type, s.url );

            // XDR has no method for setting headers O_o

            xdr.send( ( s.hasContent && s.data ) || null );
          },

          abort: function() {
            xdr.abort();
          }
        };
      });
    }
  })( jQuery );

  UARKNEWS.attach_widgets = function() {
    console.log("attaching widgets");
    $('[data-uark-news-widget-config]').each(function() {
      var $this = $(this);
      if (!$this.data('uark-news-attached')) {
        console.log("attaching to:");
        console.log($this);
        $this.data('uark-news-attached', true);
        if (typeof($this.data('uark-news-widget-config')) == 'object' && $this.data('uark-news-widget-config').json_api_endpoint) {
          var params = 
          UARKNEWS.populate_widget($this, $this.data('uark-news-widget-config'));
        } else if (UARKNEWS.isURL($this.data('uark-news-widget-config'))) {
          $.ajax({ dataType: 'json', timeout: UARKNEWS.ajax_timeout_milliseconds, url: UARKNEWS.append_callback($this.data('uark-news-widget-config'))})
            .done(function(params, textStatus, jqXHR) {
              UARKNEWS.populate_widget($this, params);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
              $this.hide();
            });
        } else {
          $this.html("Sorry, there is a problem with the widget's configuration");
        }
      }
    });
  };
  UARKNEWS.document_ready = false;
  $(document).ready(function() { console.log("document.ready"); UARKNEWS.document_ready = true; })

  UARKNEWS.repeat_until_document_ready_then_once_more = function(fn, timeout) {
    if (UARKNEWS.document_ready) {
      fn();
    } else {
      fn();
      setTimeout(function() { UARKNEWS.repeat_until_document_ready_then_once_more(fn, timeout); }, timeout);
    }
  };
  UARKNEWS.repeat_until_document_ready_then_once_more(UARKNEWS.attach_widgets, 100);
  
  jQuery.event.trigger('uarknews-loaded');
}

if(typeof jQuery=='undefined') {
    var headTag = document.getElementsByTagName("head")[0];
    var jqTag = document.createElement('script');
    jqTag.type = 'text/javascript';
    jqTag.src = 'http://code.jquery.com/jquery.min.js';
    jqTag.onload = UARKNEWS.bootstrap;
    headTag.appendChild(jqTag);
} else {
     UARKNEWS.bootstrap();
}
} // end if (!window.UARKNEWS_WIDGET_LOADED)
