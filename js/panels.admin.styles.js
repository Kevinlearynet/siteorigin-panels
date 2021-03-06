/**
 * Handles row styling.
 *
 * @copyright Greg Priday 2014
 * @license GPL 2.0 http://www.gnu.org/licenses/gpl-2.0.html
 */

jQuery( function ( $ ) {
	// Create the dialog for setting up the style
	var buttons = {};
	buttons[ panels.i10n.buttons.done ] = function () {
		$( '#grid-styles-dialog' ).dialog( 'close' );
	};

	$( '#grid-styles-dialog' ).data( 'html', $( '#grid-styles-dialog' ).html() );
	$( '#grid-styles-dialog' )
		.show()
		.dialog( {
			dialogClass: 'panels-admin-dialog',
			autoOpen: false,
			modal: false, // Disable modal so we don't mess with media editor. We'll create our own overlay.
			draggable: false,
			resizable: false,
			title: $( '#grid-styles-dialog' ).attr( 'data-title' ),
			maxHeight: Math.round( $( window ).height() * 0.8 ),
			width: 500,
			open: function () {
				var overlay = $( '<div class="siteorigin-panels ui-widget-overlay ui-widget-overlay ui-front"></div>' ).css( 'z-index', 80001 );
				$( this ).data( 'overlay', overlay ).closest( '.ui-dialog' ).before( overlay );
			},
			close: function () {
				$( this ).data( 'overlay' ).remove();

				// Copy the dialog values back to the container style value fields
				var container = $( '#grid-styles-dialog' ).data( 'container' );
				$( '#grid-styles-dialog [data-style-field]' ).each( function () {
					var $$ = $( this );
					var cf = container.find( '[data-style-field="' + $$.data( 'style-field' ) + '"]' );

					switch ( $$.data( 'style-field-type' ) ) {
					case 'checkbox':
						cf.val( $$.is( ':checked' ) ? 'true' : '' );
						break;
					default:
						cf.val( $$.val() );
						break;
					}
				} );
			},
			buttons: buttons
		} );

	panels.loadStyleValues = function ( container ) {
		$( '#grid-styles-dialog' )
			.data( 'container', container )
			.html( $( '#grid-styles-dialog' ).data( 'html' ) );

		// Copy the values of the hidden fields in the container over to the dialog.
		container.find( "[data-style-field]" ).each( function () {
			var $$ = $( this );

			// Save the dialog field
			var df = $( '#grid-styles-dialog [data-style-field="' + $$.data( 'style-field' ) + '"]' );
			switch ( df.data( 'style-field-type' ) ) {
			case 'checkbox':
				df.attr( 'checked', $$.val() ? true : false );
				break;
			default:
				df.val( $$.val() );
				break;
			}
		} );

		$( '#grid-styles-dialog' ).dialog( 'open' );

		panels.colorField();
		panels.imageField();
	};

	/**
	 * Media Uploader Field
	 *
	 * Row style field with an image uploader
	 */
	panels.colorField = function () {
		$( '#grid-styles-dialog [data-style-field-type="color"]' )
			.wpColorPicker()
			.closest( 'p' ).find( 'a' ).click( function () {
				$( '#grid-styles-dialog' ).dialog( "option", "position", "center" );
			} );
	};

	/**
	 * Media Uploader Field
	 *
	 * Row style field with an image uploader
	 */
	panels.imageField = function () {

		var $imageFields = $( '.ui-dialog [data-style-field-type="image"]' );
		$imageFields.each( function () {
			var frame;
			var $this = $( this );
			$this.on( 'click', function ( event ) {

				event.preventDefault();

				var inputName = $this.attr( 'name' );
				if ( frame ) {
					frame.open();
					return;
				}

				frame = wp.media( {
					title: "Select Image",
					multiple: false,
					frame: 'select',
					library: {
						type: 'image'
					},
					button: {
						text: 'Add Image'
					}
				} );

				frame.on( 'select', function () {
					var selection = frame.state().get( 'selection' );
					selection.each( function ( attachment ) {
						console.log( attachment );
						var media_attachment = frame.state().get( 'selection' ).first().toJSON();
						var target = $this.data( 'target-input' );
						$( 'input[name="' + target + '"]' ).val( media_attachment.url );
					} );
				} );

				frame.open();
			} );
		} );
	};
} );