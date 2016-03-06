// Globals
var socket;
var vm;

(function($) {
  /* Dom Based Routing
   * http://www.paulirish.com/2009/markup-based-unobtrusive-comprehensive-dom-ready-execution/
   */
  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var App = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages
        socket = io.connect('http://'+window.location.hostname+':3000');
        Vue.http.headers.common['X-CSRF-Token'] = $('meta[name="csrf-token"]').attr('content');
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            }
        });
      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Any Page where project amount is shown
    'project_transaction': {
      init: function() {
        // JavaScript to be fired on any page with a project transaction
        socket.on('project-transaction', function(data){
            vm.$set('project', data.project);
        }.bind(this));
      }
    },
    'project': {
        init: function(){
            vm = new Vue({
              el: '.project-amount',
              data: {
                amount : null,
                invalid: false
              },
              ready: function() {
                this.getProject(1);
                this.$watch('amount', function (amount) {
                  if(parseFloat(amount) > this.amount_left){
                    vm.$set('invalid', true);
                  } else {
                    vm.$set('invalid', false);
                  }
                })
              },
              methods: {
                getProject: function($id){
                    this.$http.get('/api/project/'+$id).success(function(project) {
                        this.$set('project', project);
                        console.log(project);
                    }).error(function(error) {
                      console.log(error);
                    });
                }
              },
              computed: {
                amount_left: function () {
                  return this.project.amount_goal - this.project.amount_reserved;
                }
              }
            });
        }
    },
    'transaction': {
        init: function(){
            vm = new Vue({
              el: '.project-transaction',
              data: {
                expires_at:  $("#expires_at").val(),
                time_now:  $("#time_now").val(),
                time_left: null,
                time_left_display: null
              },
              ready: function() {
                // Simple countdown timer
                setInterval(function(){
                    vm.$set('time_now', moment.utc(vm.$get('time_now')).add(1, 'second'));
                    var ms = moment.utc(vm.$get('expires_at')).diff(moment.utc(vm.$get('time_now')));
                    vm.$set('time_left', ms);
                    var s = moment.utc(ms).format("mm:ss");
                    vm.$set('time_left_display', s);
                    if(ms < 0){
                        // Times up! Cancel the transaction
                        // In reality it seems a bit harsh to take them away from the page 
                        // Ideally there would be logic to cancel the transaction but keep them on this page and allow them to enter a new amount
                        $( "#form" ).attr('action','transaction/cancel');
                        $( "#form" ).submit();
                    }
                }, 1000);
              },
              methods: {}
            });
        }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = App;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);
})(jQuery); // Fully reference jQuery after this point.