<?php
/**
Plugin Name: เรารักในหลวง
Description: ปลั๊กอินเปลี่ยนสีเว็บทั้งเว็บให้เป็นสีขาว-ดำ และโชว์แบนเนอร์ เพื่อไว้อาลัยการเสด็จสวรรณคตของ พระบาทสมเด็จพระปรมินทรมหาภูมิพลอดุลยเดช มหิตลาธิเบศรรามาธิบดี จักรีนฤบดินทร สยามินทราธิราช บรมนาถบพิตร รัชการที่ ๙ แก่ผู้เข้าเว็บทุกคน
Author: Mr.Pakpoom Tiwakornkit
Version: 1.0.0
Author URI: https://github.com/aptarmy/rao-ruk-nai-luang
*/

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

require_once( plugin_dir_path( __FILE__ ) . 'Titan-Framework/titan-framework-embedder.php');

/**
* All Plugin functionalities
*/
class RaoRukNaiLuang {

	/**
	 * Titan Framework
	 */
	public $titan;
	/**
	 * Black and White
	 */
	public $black_and_white_panel;
	/**
	 * Banner
	 */
	public $banner_panel;
	/**
	 * Credit
	 */
	public $credit_panel;
	
	/**
	 * add WordPress action hook
	 */
	function __construct() {
		add_action( 'init', array($this, 'setup_plugin'), 10 );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue') );
		add_action( 'wp_head', array($this, 'black_and_white'), 10 );
		add_action( 'wp_footer', array($this, 'banner'), 10 );
	}

	/**
	 * Setup plugin
	 */
	public function setup_plugin() {
		$this->titan = TitanFramework::getInstance( 'rao-ruk-nai-luang' );
		$this->setup_black_and_white_panel();
		$this->setup_banner_panel();
		$this->setup_credit_panel();
	}

	/**
	 * Setup panel for black and white
	 */
	public function setup_black_and_white_panel() {
		$this->black_and_white_panel = $this->titan->createAdminPanel( array(
		    'name' => 'เรารักในหลวง',
		    'desc' => 'ปรับสีเว็บไซต์ทั้งเว็บให้เป็นสีขาว-ดำ',
		    'icon' => plugin_dir_url(__FILE__) . 'asset/images/panel-icon.png',
		) );
		$this->black_and_white_panel->createOption( array(
		    'name' => 'เปิดหรือปิดเว็บสีขาว-ดำ',
		    'id' => 'is_black_and_white',
		    'options' => array(
		        '0' => 'ปิดการใช้งาน',
		        '1' => 'เปิดการใช้งาน',
		    ),
		    'type' => 'radio',
		    'default' => '1',
		) );
		$this->black_and_white_panel->createOption( array(
		    'name' => 'ความชัดของสีขาว-ดำ',
		    'id' => 'gray_scale_percent',
		    'desc' => 'ปรับความชัดของสีขาวดำ ถ้าปรับเป็น 100 เว็บทั้งเว็บจะเป็นสีขาวดำ ถ้าปรับเป็น 0 เว็บทั้งเว็บจะเป็นสีปกติ',
		    'type' => 'number',
		    'default' => '100',
		    'min' => '0',
		    'max' => '100',
		) );
		$this->black_and_white_panel->createOption( array(
		    'type' => 'save',
		) );
	}

	/**
	 * Gray out entire website in frontend.
	 * This method will be called by 'wp_head' hook
	 */
	public function black_and_white() {
		if (!is_admin() && $this->titan->getOption( 'is_black_and_white' )) {
			$gray_scale = $this->titan->getOption( 'gray_scale_percent' );
		?>
			<style id="row_ruk_nai_luang_black_and_white_css" type="text/css">body * {-moz-filter: grayscale(<?php echo $gray_scale; ?>%); -webkit-filter: grayscale(<?php echo $gray_scale; ?>%); filter: grayscale(<?php echo $gray_scale; ?>%); }</style>
		<?php
		}
	}

	/**
	 * Setup panel setting for Banner
	 */
	public function setup_banner_panel() {
		$this->banner_panel = $this->black_and_white_panel->createAdminPanel( array(
		    'name' => 'ตั้งค่าแบนเนอร์',
		    'desc' => 'แบนเนอร์แสดงความอาลัยต่อการสวรรณคตของพระบาทสมเด็จพระปรมินทรมหาภูมิพลอดุลยเดช'
		) );
		$this->banner_panel->createOption( array(
		    'name' => 'โชว์แบนเนอร์',
		    'id' => 'is_show_banner',
		    'options' => array(
		        '0' => 'ปิดการใช้งาน',
		        '1' => 'เปิดการใช้งาน',
		    ),
		    'desc' => 'แบนเนอร์นี้จะแสดงให้ผู้เข้าเว็บเห็นครั้งเดียว ถ้าปิดแท็บแล้วเปิดใหม่ แบนเนอร์ก็จะโชว์อีกรอบ (หรือเรียกสั้นๆว่า session storage)',
		    'type' => 'radio',
		    'default' => '1',
		) );
		$this->banner_panel->createOption( array(
		    'name' => 'ซ่อนแบนเนอร์เมื่อหน้าจอเล็กกว่ากี่พิกเซล',
		    'id' => 'hide_banner_at',
		    'desc' => 'ค่าดีฟอลส์คือ 768',
		    'type' => 'number',
		    'default' => '768',
		    'max' => '2000',
		) );
		$this->banner_panel->createOption( array(
		      'name' => 'ชื่อองค์กร, ชื่อเว็บไซต์ หรือบริษัท',
		      'id' => 'site_owners',
		      'type' => 'text',
		      'desc' => 'ชื่อนี้จะแสดงบนแบนเนอร์ ในประโยค "ด้วยเกล้าด้วยกระหม่อมขอเดชะ ข้าพระพุทธเจ้า ทีมงานเว็บไซต์ '. get_bloginfo('name') . '"',
		      'default' => 'ทีมงาน ' . get_bloginfo('name'),
		) );
		$this->banner_panel->createOption( array(
		    'name' => 'เลือกรูปแบนเนอร์',
		    'desc' => 'รูปนี้เวลาแสดงในหน้าเว็บจะเป็นสีขาวดำ',
		    'id' => 'banner',
		    'type' => 'radio-image',
		    'options' => array(
		        'banner_1' => plugin_dir_url(__FILE__) . 'asset/images/banner_1_thumb.png',
		        'banner_2' => plugin_dir_url(__FILE__) . 'asset/images/banner_2_thumb.png',
		    ),
		    'default' => 'banner_1',
		) );
		$this->banner_panel->createOption( array(
		    'type' => 'save',
		) );
	}

	/**
	 * Show banner in frontend
	 */
	public function banner() {
		if (is_admin() || !$this->titan->getOption( 'is_show_banner' )) {
			return;
		}
		?>
		<script type="text/javascript">
			(function($){
				$(document).ready(function(){
					!sessionStorage.rao_ruk_nai_luang_displayed &&
					$('body').append('<div class="rao_ruk_nai_luang_background <?php echo $this->titan->getOption( 'banner' ); ?>"><img class="rao_ruk_nai_luang_close" src="<?php echo plugin_dir_url(__FILE__); ?>asset/images/close-icon.png"><div class="rao_ruk_nai_luang_container"><img class="rao_ruk_nai_luang_img" src="<?php echo plugin_dir_url(__FILE__); ?>asset/images/<?php echo $this->titan->getOption( 'banner' ); ?>.png"><span class="rao_ruk_nai_luang_site_owners">ข้าพระพุทธเจ้า <?php echo $this->titan->getOption( 'site_owners' ); ?></span></div></div>'),
					$('body').on('click', '.rao_ruk_nai_luang_close', function(){ $('.rao_ruk_nai_luang_background').removeClass('show'); setTimeout(function(){$('.rao_ruk_nai_luang_background').remove();}, 300); }),
					sessionStorage.rao_ruk_nai_luang_displayed = true;
					setTimeout(function(){$('.rao_ruk_nai_luang_background').addClass('show');}, 300);
				});
			})(jQuery);
		</script>
		<style type="text/css">
			.rao_ruk_nai_luang_background { opacity: 0; display: none; pointer-events: none; }
			@media (min-width: <?php echo $this->titan->getOption( 'hide_banner_at' ); ?>px) {
				.rao_ruk_nai_luang_background { display: block; z-index: 100000; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); transition: all 0.3s ease; }
				.rao_ruk_nai_luang_background.show { opacity: 1; pointer-events: initial; }
				.rao_ruk_nai_luang_container { width: 80vw; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 100%; }
				.rao_ruk_nai_luang_img { width: 100%; }
				.rao_ruk_nai_luang_site_owners { font-family: 'Kanit', sans-serif; pointer-events: none; }
				.banner_1 .rao_ruk_nai_luang_site_owners { position: absolute; top: 88%; left: 0%; right: 0%; color: #fdc655; text-shadow: 0px 0px 10px black; text-align: center; font-size: 18px; }
				.banner_2 .rao_ruk_nai_luang_site_owners { position: absolute; top: 74%; left: 38%; right: 5%; color: #fdc655; text-shadow: 0px 0px 10px black; text-align: center; font-size: 20px; }
				.rao_ruk_nai_luang_close { position: fixed; width: 30px; right: 15px; top: 15px; cursor: pointer; transition: transform 0.3s ease; }
				.rao_ruk_nai_luang_close:hover { transform: rotateZ(90deg); }
			}
		</style>
		<?php
	}

	public function setup_credit_panel() {

		$this->credit_panel = $this->black_and_white_panel->createAdminPanel( array(
		    'name' => 'เครดิต'
		) );

		$this->credit_panel->createOption( array(
			'type' => 'custom',
			'custom' => '
				<article class="markdown-body">
				<p>ปลั๊กอินนี้จะเกิดขึ้นไม่ได้ หากไม่มีวัตถุดิบเหล่านี้ ผมจึงขอให้เครดิตเครื่องมือ(framework) รูปภาพ กับฟอนต์ที่เอามาทำแบนเนอร์ และปลั๊กอินครับ ผมขอขอบคุณเจ้าของผลงานทุกๆท่านไว้ ณ ที่นี้ด้วยครับ</p>
				<h1>ขอบคุณ framework จาก</h1>
				<ul>
				<li><a href="http://www.titanframework.net/">http://www.titanframework.net/</a></li>
				</ul>
				<h1>ขอบคุณรูปภาพจาก</h1>
				<ul>
				<li><a href="http://www.sangkhacrst.org/?attachment_id=21">http://www.sangkhacrst.org/?attachment_id=21</a></li>
				<li><a href="http://www.bkk1.in.th/Topic.aspx?TopicID=204872">http://www.bkk1.in.th/Topic.aspx?TopicID=204872</a></li>
				<li><a href="http://www.overstockart.com/frame/victorian-gold-frame-20x24">http://www.overstockart.com/frame/victorian-gold-frame-20x24</a></li>
				</ul>
				<h1>ขอบคุณฟอนต์จาก</h1>
				<ul>
				<li><a href="http://www.f0nt.com/release/saruns-manorah/">http://www.f0nt.com/release/saruns-manorah/</a></li>
				</ul>
				<h1>นักพัฒนาปลั๊กอิน</h1>
				<ul>
				<li><a href="https://github.com/aptarmy">https://github.com/aptarmy</a></li>
				</ul>
				</article>
			'
		) );
	}

	public function enqueue() {
		wp_enqueue_style( 'rao_ruk_nai_luang_font_kanit', 'https://fonts.googleapis.com/css?family=Kanit', array() );
	}
}
new RaoRukNaiLuang();