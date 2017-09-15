<?php
/**
 * Class ReferenceTest.
 *
 * @package Media_Deduper_Pro
 */

/**
 * Test case for attachment detection/replacement.
 */
class ReferenceTest extends PHPUnit_Framework_TestCase {

	/**
	 * An MDD_Reference_Handler object.
	 *
	 * @var MDD_Reference_Handler
	 */
	public $reference_handler;

	/**
	 * Called before each test.
	 */
	public function setUp() {
		\WP_Mock::setUp();
		$this->reference_handler = new MDD_Reference_Handler();
	}

	/**
	 * Called after each test.
	 */
	public function tearDown() {
		\WP_Mock::tearDown();
	}

	/**
	 * MDD_Reference_Handler::detect_int() should detect an attachment reference
	 * in a single attachment ID integer.
	 */
	function test_detect_int_detects_single_attachment_id() {
		$this->expect_check_attachment_id( 123 );
		$refs = $this->reference_handler->detect_int( array(), 123 );
		$this->assertSame( array( 123 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::detect_int() should detect an attachment reference
	 * in a single attachment ID string.
	 */
	function test_detect_int_detects_single_attachment_id_string() {
		$this->expect_check_attachment_id( '123' );
		$refs = $this->reference_handler->detect_int( array(), '123' );
		$this->assertSame( array( 123 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::detect_int() should ignore IDs of non-attachments.
	 */
	function test_detect_int_ignores_single_non_attachment_id() {
		$this->expect_check_attachment_id( 123, 'post' );
		$refs = $this->reference_handler->detect_int( array(), 123 );
		$this->assertSame( array(), $refs );
	}

	/**
	 * MDD_Reference_Handler::detect_int() should ignore IDs of non-posts.
	 */
	function test_detect_int_ignores_single_non_post_id() {
		$this->expect_check_attachment_id( 123, false );
		$refs = $this->reference_handler->detect_int( array(), 123 );
		$this->assertSame( array(), $refs );
	}

	/**
	 * MDD_Reference_Handler::replace_int() should replace integer values with
	 * integers.
	 */
	function test_replace_int_replaces_ints() {
		$replacement = $this->reference_handler->replace_int( 123, 123, 234 );
		$this->assertSame( 234, $replacement );
	}

	/**
	 * MDD_Reference_Handler::replace_int() should replace string values with
	 * strings.
	 */
	function test_replace_int_replaces_strings() {
		$replacement = $this->reference_handler->replace_int( '123', 123, 234 );
		$this->assertSame( '234', $replacement );
	}

	/**
	 * MDD_Reference_Handler::detect_multi_int() should detect attachment IDs and
	 * ignore non-attachment/non-post IDs in an array.
	 */
	function test_detect_multi_int_detects_attachments_and_ignores_others() {
		$this->expect_check_attachment_id( 123, false );
		$this->expect_check_attachment_id( 456, 'attachment' );
		$this->expect_check_attachment_id( 789, 'page' );
		$this->expect_check_attachment_id( 12, 'attachment' );
		$refs = $this->reference_handler->detect_multi_int( array(), array( 123, 456, 789, 12 ) );
		$this->assertSame( array( 456, 12 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::detect_multi_int() should detect attachment IDs and
	 * ignore non-attachment/non-post IDs in a string.
	 */
	function test_detect_multi_int_works_on_strings_too() {
		$this->expect_check_attachment_id( 123, false );
		$this->expect_check_attachment_id( 456, 'attachment' );
		$this->expect_check_attachment_id( 789, 'page' );
		$this->expect_check_attachment_id( 12, 'attachment' );
		$refs = $this->reference_handler->detect_multi_int( array(), '123,haha look out!!,456,789,12' );
		$this->assertSame( array( 456, 12 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::replace_multi_int() should replace the given 'old'
	 * value and ignore others in an array.
	 */
	function test_replace_multi_int_replaces_old_value_and_ignores_others() {
		$replacement = $this->reference_handler->replace_multi_int( array( 123, 456, 789, 12 ), 123, 234 );
		$this->assertSame( array( 234, 456, 789, 12 ), $replacement );
	}

	/**
	 * MDD_Reference_Handler::replace_multi_int() should replace the given 'old'
	 * value and ignore others in a string.
	 */
	function test_replace_multi_int_works_on_strings_too() {
		$replacement = $this->reference_handler->replace_multi_int( '123,haha look out!!,456,789,12', 123, 234 );
		$this->assertSame( '234,haha look out!!,456,789,12', $replacement );
	}

	/**
	 * MDD_Reference_Handler::detect_urls() should detect an attachment reference
	 * in a single absolute attachment URL.
	 */
	function test_detect_urls_detects_single_absolute_url() {

		$this->expect_get_attachment_url_regex();
		$this->expect_get_attachment_id_from_filename( '2017/05/test.jpg', 123 );

		$url = 'http://test.biz/wp-content/uploads/2017/05/test.jpg';
		$refs = $this->reference_handler->detect_urls( array(), $url );

		$this->assertSame( array( 123 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::detect_urls() should detect an attachment reference
	 * in a single absolute resized attachment URL.
	 */
	function test_detect_urls_detects_single_absolute_resized_url() {

		$this->expect_get_attachment_url_regex();
		$this->expect_get_attachment_id_from_filename( '2017/05/test-200x200.jpg', false );
		$this->expect_get_attachment_id_from_filename( '2017/05/test.jpg', 123 );

		$url = 'http://test.biz/wp-content/uploads/2017/05/test-200x200.jpg';
		$refs = $this->reference_handler->detect_urls( array(), $url );

		$this->assertSame( array( 123 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::detect_urls() should detect an attachment reference
	 * in a single protocol-relative attachment URL.
	 */
	function test_detect_urls_detects_single_protocol_relative_url() {

		$this->expect_get_attachment_url_regex();
		$this->expect_get_attachment_id_from_filename( '2017/05/test.jpg', 123 );

		$url = '//test.biz/wp-content/uploads/2017/05/test.jpg';
		$refs = $this->reference_handler->detect_urls( array(), $url );

		$this->assertSame( array( 123 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::detect_urls() should detect an attachment reference
	 * in a single relative attachment URL.
	 */
	function test_detect_urls_detects_single_relative_url() {

		$this->expect_get_attachment_url_regex();
		$this->expect_get_attachment_id_from_filename( '2017/05/test.jpg', 123 );

		$url = '/wp-content/uploads/2017/05/test.jpg';
		$refs = $this->reference_handler->detect_urls( array(), $url );

		$this->assertSame( array( 123 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::detect_urls() should detect all local URLs in the
	 * given string.
	 */
	function test_detect_urls_detects_multiple_urls() {

		$this->expect_get_attachment_url_regex();
		$this->expect_get_attachment_id_from_filename( '2017/05/linked-file.jpg', 124 );
		$this->expect_get_attachment_id_from_filename( '2017/01/embedded-file.jpg', 125 );

		$value = '<a href="/wp-content/uploads/2017/05/linked-file.jpg"><img src="http://test.biz/wp-content/uploads/2017/01/embedded-file.jpg"></a>';
		$refs = $this->reference_handler->detect_urls( array(), $value );

		$this->assertSame( array( 124, 125 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::detect_urls() should ignore external URLs.
	 */
	function test_detect_urls_ignores_external_urls() {

		$this->expect_get_attachment_url_regex();

		$url = 'http://othersite.gov/wp-content/uploads/2017/05/test.jpg';
		$refs = $this->reference_handler->detect_urls( array(), $url );

		$this->assertSame( array(), $refs );
	}

	/**
	 * MDD_Reference_Handler::replace_url() should replace a single absolute
	 * attachment URL.
	 */
	function test_replace_url_replaces_single_absolute_url() {

		$this->expect_attachments();
		$url = 'http://test.biz/wp-content/uploads/2017/05/test.jpg';
		$replaced = $this->reference_handler->replace_url( $url, 12, 34 );

		$this->assertSame( 'http://test.biz/wp-content/uploads/2017/06/test-new.jpg', $replaced );
	}

	/**
	 * MDD_Reference_Handler::replace_url() should replace a single absolute
	 * resized attachment URL.
	 */
	function test_replace_url_replaces_single_absolute_resized_url() {

		$this->expect_attachments();
		$url = 'http://test.biz/wp-content/uploads/2017/05/test-32x32.jpg';
		$replaced = $this->reference_handler->replace_url( $url, 12, 34 );

		$this->assertSame( 'http://test.biz/wp-content/uploads/2017/06/test-new-32x32.jpg', $replaced );
	}

	/**
	 * MDD_Reference_Handler::replace_url() should replace a single
	 * protocol-relative attachment URL.
	 */
	function test_replace_url_replaces_single_protocol_relative_url() {

		$this->expect_attachments();
		$url = '//test.biz/wp-content/uploads/2017/05/test.jpg';
		$replaced = $this->reference_handler->replace_url( $url, 12, 34 );

		$this->assertSame( '//test.biz/wp-content/uploads/2017/06/test-new.jpg', $replaced );
	}

	/**
	 * MDD_Reference_Handler::replace_url() should replace a single relative
	 * attachment URL.
	 */
	function test_replace_url_replaces_single_relative_url() {

		$this->expect_attachments();
		$url = '/wp-content/uploads/2017/05/test.jpg';
		$replaced = $this->reference_handler->replace_url( $url, 12, 34 );

		$this->assertSame( '/wp-content/uploads/2017/06/test-new.jpg', $replaced );
	}

	/**
	 * MDD_Reference_Handler::replace_url() should ignore external URLs.
	 */
	function test_replace_url_ignores_external_urls() {

		$this->expect_attachments();
		$url = 'http://othersite.gov/wp-content/uploads/2017/05/test.jpg';
		$replaced = $this->reference_handler->replace_url( $url, 12, 34 );

		$this->assertSame( 'http://othersite.gov/wp-content/uploads/2017/05/test.jpg', $replaced );
	}

	/**
	 * MDD_Reference_Handler::replace_multi_url() should replace all local URLs in
	 * the given string.
	 */
	function test_replace_multi_url_replaces_multiple_urls() {

		$this->expect_attachments();
		$this->expect_get_attachment_url_regex();
		$html = '<a href="/wp-content/uploads/2017/05/test.jpg"><img src="http://test.biz/wp-content/uploads/2017/05/test-32x32.jpg"></a>';
		$replaced = $this->reference_handler->replace_multi_url( $html, 12, 34 );

		$this->assertSame( '<a href="/wp-content/uploads/2017/06/test-new.jpg"><img src="http://test.biz/wp-content/uploads/2017/06/test-new-32x32.jpg"></a>', $replaced );
	}

	/**
	 * MDD_Reference_Handler::replace_multi_url() should ignore URLs that don't
	 * need to be replaced.
	 */
	function test_replace_multi_url_ignores_irrelevant_urls() {

		$this->expect_attachments();
		$this->expect_get_attachment_url_regex();
		$html = '<a href="/wp-content/uploads/2012/12/else.jpg"><img src="http://test.biz/wp-content/uploads/2012/12/else-32x32.jpg"></a>';
		$replaced = $this->reference_handler->replace_multi_url( $html, 12, 34 );

		$this->assertSame( '<a href="/wp-content/uploads/2012/12/else.jpg"><img src="http://test.biz/wp-content/uploads/2012/12/else-32x32.jpg"></a>', $replaced );
	}

	/**
	 * MDD_Reference_Handler::detect_gallery_ids() should detect all numeric IDs
	 * in gallery shortcodes that use either single or double quotes.
	 */
	function test_detect_gallery_ids_detects_ids() {

		$content = 'test test [gallery ids="2,3,4,5"] [gallery another-attribute=\'1\' ids=\'7,6,5,23,3\']';
		$refs = $this->reference_handler->detect_gallery_ids( array(), $content );

		// Renumber keys in $refs, otherwise keys won't match the expected array!
		$refs = array_values( $refs );

		$this->assertSame( array( 2, 3, 4, 5, 7, 6, 23 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::replace_gallery_ids() should replace numeric IDs in
	 * gallery shortcodes that use either single or double quotes, and ignore
	 * partial matches (i.e. '23' should be ignored if the old ID to replace is
	 * '3').
	 */
	function test_replace_gallery_ids_replaces_ids() {

		$content = 'test test [gallery ids="2,3,4,5"] [gallery another-attribute=\'1\' ids=\'7,6,5,23,3\']';
		$content = $this->reference_handler->replace_gallery_ids( $content, 3, 21 );

		$this->assertSame( 'test test [gallery ids="2,21,4,5"] [gallery another-attribute=\'1\' ids=\'7,6,5,23,21\']', $content );
	}

	/**
	 * MDD_Reference_Handler::detect_caption_ids() should detect all ID attributes
	 * like "attachment_###" in caption shortcodes that use single, double, or no
	 * quotes.
	 */
	function test_detect_caption_ids_detects_ids() {

		$content = 'test test [caption id="attachment_2"]<img src="test.jpg"> test test[/caption]
			[caption id="something-arbitrary"]<img src="toast.jpg"> toast toast[/caption]
			[caption id=\'attachment_3\']<img src="tasty.jpg"> tasty toast[/caption]
			[caption id=attachment_200]<img src="toes.jpg"> yuck[/caption]';
		$refs = $this->reference_handler->detect_caption_ids( array(), $content );

		$this->assertSame( array( 2, 3, 200 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::replace_caption_ids() should replace all ID
	 * attributes like "attachment_###" in caption shortcodes that use single,
	 * double, or no quotes.
	 */
	function test_replace_caption_ids_replaces_ids() {

		$content = 'test test [caption id="attachment_6"]<img src="test.jpg"> test test[/caption]
			[caption id="something-arbitrary"]<img src="test.jpg"> toast toast[/caption]
			[caption id=\'attachment_6\']<img src="test.jpg"> tasty toast[/caption]
			[caption id=attachment_6]<img src="test.jpg"> these images are all the same because the id is the same :\[/caption]';
		$content = $this->reference_handler->replace_caption_ids( $content, 6, 7 );

		$this->assertSame( 'test test [caption id="attachment_7"]<img src="test.jpg"> test test[/caption]
			[caption id="something-arbitrary"]<img src="test.jpg"> toast toast[/caption]
			[caption id=\'attachment_7\']<img src="test.jpg"> tasty toast[/caption]
			[caption id=attachment_7]<img src="test.jpg"> these images are all the same because the id is the same :\[/caption]', $content );
	}

	/**
	 * MDD_Reference_Handler::detect_img_classes() should detect IDs in class
	 * attributes like "wp-image-###" in img tags that use single or double
	 * quotes, including images with more than one class.
	 */
	function test_detect_img_classes_detects_ids() {

		$content = '<img src="test.jpg" class="wp-image-22">
			<img src="test-2.jpg" class="wp-image-33 alignright">
			<img src="test-3.jpg" class=\'some-class wp-image-44\'>';
		$refs = $this->reference_handler->detect_img_classes( array(), $content );

		$this->assertSame( array( 22, 33, 44 ), $refs );
	}

	/**
	 * MDD_Reference_Handler::replace_img_classes() should replace all class
	 * attributes like "wp-image-###" in img tags that use single or double
	 * quotes.
	 */
	function test_replace_img_classes_replaces_ids() {

		$content = '<img src="test.jpg" class="wp-image-22">
			<img src="test.jpg" class="wp-image-22 alignright">
			<img src="test.jgp" class=\'some-class wp-image-22\'>';
		$content = $this->reference_handler->replace_img_classes( $content, 22, 760 );

		$this->assertSame( '<img src="test.jpg" class="wp-image-760">
			<img src="test.jpg" class="wp-image-760 alignright">
			<img src="test.jgp" class=\'some-class wp-image-760\'>', $content );
	}

	/**
	 * Mock core functions used by MDD_Reference_Handler::check_attachment_id().
	 *
	 * @param any         $value     The value passed to detect_int().
	 * @param string|bool $post_type The post type that get_post() should return a
	 *                               mock-post for, or FALSE if get_post shouldn't
	 *                               return anything.
	 */
	public function expect_check_attachment_id( $value, $post_type = 'attachment' ) {

		$id = abs( intval( $value ) );

		$get_post = \WP_Mock::userFunction( 'get_post' )
			->with( $id )
			->once();

		if ( $post_type ) {
			// If a truthy post type was provided, return a mock post object with that
			// post type.
			$get_post->andReturn( (object) array(
				'ID'        => $id,
				'post_type' => $post_type,
			) );
		} else {
			// If $post_type is falsey, return NULL, as though get_post() didn't find
			// a post with the gtiven ID.
			$get_post->andReturn( null );
		}
	}

	/**
	 * Mock core functions used by
	 * MDD_Reference_Handler::get_attachment_url_regex().
	 */
	public function expect_get_attachment_url_regex() {
		\WP_Mock::userFunction( 'wp_upload_dir' )
			->with( null, false )
			->andReturn( array(
				'baseurl' => 'http://test.biz/wp-content/uploads',
			) )
			->once();
		\WP_Mock::userFunction( 'home_url' )
			->andReturn( 'http://test.biz' )
			->once();
		\WP_Mock::userFunction( 'wp_get_mime_types' )
			->andReturn( array(
				'jpg|jpeg|jpe' => 'image/jpeg',
			) )
			->once();
	}

	/**
	 * Mock core functions used by
	 * MDD_Reference_Handler::get_attachment_id_from_filename().
	 *
	 * @param string   $file Expect a call to get_posts() looking for an
	 *                       attachment post with this filename.
	 * @param int|bool $id   Return a mock post with this ID, or no post if FALSE.
	 */
	public function expect_get_attachment_id_from_filename( $file, $id ) {

		$get_posts = \WP_Mock::userFunction( 'get_posts' )
			->with( array(
				'numberposts' => 1,
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'meta_key'    => '_wp_attached_file',
				'meta_value'  => $file,
			) )
			->once();
		if ( $id ) {
			// If a truthy ID was provided, return a mock post with that ID.
			$get_posts->andReturn( array(
				(object) array(
					'ID' => $id,
				),
			) );
		} else {
			// If $id is falsey, return nothing.
			$get_posts->andReturn( array() );
		}
	}

	/**
	 * Create a couple mock attachments for testing
	 * MDD_Reference_Handler::replace_url() or
	 * MDD_Reference_Handler::replace_multi_url().
	 */
	public function expect_attachments() {
		$this->expect_attachment( 12, 'http://test.biz/wp-content/uploads/2017/05/test.jpg', array(
			'ickle' => array(
				'file' => 'test-32x32.jpg',
			),
		) );
		$this->expect_attachment( 34, 'http://test.biz/wp-content/uploads/2017/06/test-new.jpg', array(
			'ickle' => array(
				'file' => 'test-new-32x32.jpg',
			),
		) );
	}

	/**
	 * Mock core functions used by MDD_Reference_Handler::replace_url() and
	 * MDD_Reference_Handler::replace_multi_url().
	 *
	 * @param int        $id    A (ficitonal) attachment ID.
	 * @param string     $url   The URL for the attachment file.
	 * @param array|bool $sizes An array of image sizes like
	 *                          wp_get_attachment_metadata() would return, or
	 *                          FALSE if the attachment isn't an image.
	 */
	public function expect_attachment( $id, $url, $sizes ) {

		$is_image = ! empty( $sizes );
		if ( $is_image ) {
			$metadata = array(
				'sizes' => $sizes,
			);
		} else {
			$metadata = array();
		}

		\WP_Mock::userFunction( 'wp_get_attachment_url' )
			->with( $id )
			->andReturn( $url )
			->once();
		\WP_Mock::userFunction( 'wp_get_attachment_metadata' )
			->with( $id )
			->andReturn( $metadata )
			->once();
		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->with( $id )
			->andReturn( $is_image )
			->once();
	}
}
