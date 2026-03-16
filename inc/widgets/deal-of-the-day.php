<?php
/**
 * Zendotech Deal of the Day Widget
 *
 * Renders a "Deal of the Days" section with:
 *  - Pill-shaped header with title + live countdown (D / H / M / S)
 *  - Dashed-red bordered product carousel
 *  - Product cards matching the Zendotech product-card design
 *  - "Hot" badge on configurable products
 *  - Prev / Next carousel arrows
 *
 * Drop this file in:  inc/widgets/deal-of-the-day.php
 * Then add the filename to the $widgets array in inc/elementor-widgets.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Zendotech_Deal_Of_The_Day_Widget extends \Elementor\Widget_Base {

	public function get_name()       { return 'zendotech_deal_of_the_day'; }
	public function get_title()      { return __( 'Deal of the Day', 'zendotech' ); }
	public function get_icon()       { return 'eicon-flash'; }
	public function get_categories() { return [ 'zendotech' ]; }

	/* ============================================================
	   CONTROLS
	   ============================================================ */
	protected function register_controls() {

		/* ---- Content: Header ---- */
		$this->start_controls_section( 'section_header', [
			'label' => __( 'Header', 'zendotech' ),
		] );

		$this->add_control( 'section_title', [
			'label'   => __( 'Section Title', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'DEAL OF THE DAYS.',
		] );

		$this->add_control( 'countdown_end', [
			'label'          => __( 'Deal Ends At', 'zendotech' ),
			'type'           => \Elementor\Controls_Manager::DATE_TIME,
			'default'        => gmdate( 'Y-m-d H:i', strtotime( '+3 days' ) ),
			'description'    => __( 'UTC date/time when the deal expires.', 'zendotech' ),
			'picker_options' => [ 'enableTime' => true ],
		] );

		$this->end_controls_section();

		/* ---- Content: Products ---- */
		$this->start_controls_section( 'section_products', [
			'label' => __( 'Products', 'zendotech' ),
		] );

		$this->add_control( 'product_source', [
			'label'   => __( 'Product Source', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'on_sale',
			'options' => [
				'on_sale'  => __( 'On Sale', 'zendotech' ),
				'featured' => __( 'Featured', 'zendotech' ),
				'popular'  => __( 'Most Popular', 'zendotech' ),
				'latest'   => __( 'Latest', 'zendotech' ),
			],
		] );

		$this->add_control( 'product_count', [
			'label'   => __( 'Products to Load', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 8,
			'min'     => 2,
			'max'     => 20,
		] );

		$this->add_control( 'cards_visible', [
			'label'       => __( 'Cards Visible at Once', 'zendotech' ),
			'type'        => \Elementor\Controls_Manager::NUMBER,
			'default'     => 4,
			'min'         => 2,
			'max'         => 6,
			'description' => __( 'Desktop. Tablet shows 2, mobile shows 1.', 'zendotech' ),
		] );

		$this->add_control( 'hot_badge_label', [
			'label'   => __( '"Hot" Badge Label', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Hot',
		] );

		$this->end_controls_section();

		/* ---- Style: Wrapper ---- */
		$this->start_controls_section( 'style_wrapper', [
			'label' => __( 'Section Border', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'border_color', [
			'label'     => __( 'Dashed Border Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ea1d22',
			'selectors' => [ '{{WRAPPER}} .dotd-inner' => 'border-color: {{VALUE}};' ],
		] );

		$this->add_control( 'border_radius', [
			'label'      => __( 'Border Radius', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'    => [ 'size' => 16, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .dotd-inner' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		/* ---- Style: Header Pill ---- */
		$this->start_controls_section( 'style_pill', [
			'label' => __( 'Header Pill', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'pill_bg', [
			'label'     => __( 'Pill Background', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .dotd-pill' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'pill_border_color', [
			'label'     => __( 'Pill Border Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#e0e2e8',
			'selectors' => [ '{{WRAPPER}} .dotd-pill' => 'border-color: {{VALUE}};' ],
		] );

		$this->add_control( 'title_color', [
			'label'     => __( 'Title Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#121218',
			'selectors' => [ '{{WRAPPER}} .dotd-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'endin_color', [
			'label'     => __( '"End in" Label Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#555566',
			'selectors' => [ '{{WRAPPER}} .dotd-end-label' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'cd_bg', [
			'label'     => __( 'Countdown Circle BG', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ea1d22',
			'selectors' => [ '{{WRAPPER}} .dotd-cd-box' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'cd_color', [
			'label'     => __( 'Countdown Text Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .dotd-cd-box' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_section();

		/* ---- Style: Product Card ---- */
		$this->start_controls_section( 'style_card', [
			'label' => __( 'Product Card', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'card_bg', [
			'label'     => __( 'Card Background', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .dotd-product-card' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'card_radius', [
			'label'      => __( 'Card Border Radius', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
			'selectors'  => [ '{{WRAPPER}} .dotd-product-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [
			'name'     => 'card_shadow',
			'selector' => '{{WRAPPER}} .dotd-product-card',
		] );

		$this->add_control( 'hot_badge_bg', [
			'label'     => __( 'Hot Badge BG', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#F59E0B',
			'selectors' => [ '{{WRAPPER}} .dotd-hot-badge' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'price_color', [
			'label'     => __( 'Price Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#3B3B98',
			'selectors' => [ '{{WRAPPER}} .dotd-price' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_section();

		/* ---- Style: Arrows ---- */
		$this->start_controls_section( 'style_arrows', [
			'label' => __( 'Navigation Arrows', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'arrow_bg', [
			'label'     => __( 'Arrow Background', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .dotd-arrow' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'arrow_color', [
			'label'     => __( 'Arrow Icon Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#555566',
			'selectors' => [ '{{WRAPPER}} .dotd-arrow' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'arrow_hover_bg', [
			'label'     => __( 'Arrow Hover BG', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ea1d22',
			'selectors' => [ '{{WRAPPER}} .dotd-arrow:hover' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'arrow_hover_color', [
			'label'     => __( 'Arrow Hover Icon Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .dotd-arrow:hover' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/* ============================================================
	   RENDER
	   ============================================================ */
	protected function render() {
		$s             = $this->get_settings_for_display();
		$uid           = 'dotd-' . $this->get_id();
		$visible       = max( 2, intval( $s['cards_visible'] ) );
		$countdown_end = ! empty( $s['countdown_end'] ) ? $s['countdown_end'] : gmdate( 'Y-m-d H:i', strtotime( '+3 days' ) );
		$end_ts        = strtotime( $countdown_end );

		$products = $this->get_products( $s['product_source'], intval( $s['product_count'] ) );

		if ( empty( $products ) ) {
			echo '<p style="padding:20px;color:var(--text-muted,#8E8EA0);">' . __( 'No products found for "Deal of the Day".', 'zendotech' ) . '</p>';
			return;
		}
		?>
		<section class="section dotd-section" id="<?php echo esc_attr( $uid ); ?>">
			<div class="container">

				<!-- ── Header pill ── -->
				<div class="dotd-pill-wrap">
					<div class="dotd-pill">
						<span class="dotd-title"><?php echo esc_html( $s['section_title'] ); ?></span>
						<span class="dotd-end-label">End in :</span>
						<div class="dotd-countdown" data-end="<?php echo esc_attr( $end_ts ); ?>">
							<span class="dotd-cd-box" data-unit="d">00D</span>
							<span class="dotd-cd-box" data-unit="h">00H</span>
							<span class="dotd-cd-box" data-unit="m">00M</span>
							<span class="dotd-cd-box" data-unit="s">00S</span>
						</div>
					</div>
				</div>

				<!-- ── Dashed border container ── -->
				<div class="dotd-inner">

					<!-- prev/next arrows -->
					<button class="dotd-arrow dotd-prev" aria-label="<?php esc_attr_e( 'Previous', 'zendotech' ); ?>">
						<i class="fa-solid fa-chevron-left"></i>
					</button>
					<button class="dotd-arrow dotd-next" aria-label="<?php esc_attr_e( 'Next', 'zendotech' ); ?>">
						<i class="fa-solid fa-chevron-right"></i>
					</button>

					<!-- scrollable track -->
					<div class="dotd-track-wrap">
						<div class="dotd-track" style="--dotd-visible:<?php echo esc_attr( $visible ); ?>;">
							<?php foreach ( $products as $product ) :
								if ( ! $product || ! is_a( $product, 'WC_Product' ) ) continue;
								$this->render_card( $product, $s );
							endforeach; ?>
						</div>
					</div>

				</div><!-- .dotd-inner -->
			</div>
		</section>

		<!-- ── Inline CSS (scoped to this widget instance) ── -->
		<style>
		/* ---------- Section ---------- */
		#<?php echo esc_attr( $uid ); ?> .dotd-section { padding: 0; }

		/* ---------- Pill header ---------- */
		#<?php echo esc_attr( $uid ); ?> .dotd-pill-wrap {
			display: flex;
			justify-content: center;
			position: relative;
			z-index: 2;
			margin-bottom: -1px; /* overlaps border line */
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-pill {
			display: inline-flex;
			align-items: center;
			gap: 14px;
			background: #fff;
			border: 1px solid #e0e2e8;
			border-radius: 50px;
			padding: 10px 24px;
			box-shadow: 0 2px 10px rgba(0,0,0,.06);
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-title {
			font-size: 16px;
			font-weight: 800;
			letter-spacing: .3px;
			color: #121218;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-title span.dot {
			color: var(--cta, #ea1d22);
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-end-label {
			font-size: 13px;
			color: #555566;
			font-weight: 500;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-countdown {
			display: flex;
			gap: 6px;
			align-items: center;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-cd-box {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			min-width: 38px;
			height: 38px;
			border-radius: 50%;
			background: var(--cta, #ea1d22);
			color: #fff;
			font-size: 11px;
			font-weight: 700;
			letter-spacing: .4px;
		}

		/* ---------- Dashed border wrapper ---------- */
		#<?php echo esc_attr( $uid ); ?> .dotd-inner {
			position: relative;
			border: 2px dashed var(--cta, #ea1d22);
			border-radius: 16px;
			padding: 32px 52px;
			background: #fff;
		}

		/* ---------- Arrows ---------- */
		#<?php echo esc_attr( $uid ); ?> .dotd-arrow {
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			z-index: 10;
			width: 36px;
			height: 36px;
			border-radius: 50%;
			border: 1px solid #e0e2e8;
			background: #fff;
			color: #555566;
			font-size: 13px;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: all .25s cubic-bezier(.4,0,.2,1);
			box-shadow: 0 2px 8px rgba(0,0,0,.08);
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-arrow:hover {
			background: var(--cta, #ea1d22);
			border-color: var(--cta, #ea1d22);
			color: #fff;
			box-shadow: 0 4px 14px rgba(234,29,34,.25);
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-prev { left: 12px; }
		#<?php echo esc_attr( $uid ); ?> .dotd-next { right: 12px; }

		/* ---------- Track ---------- */
		#<?php echo esc_attr( $uid ); ?> .dotd-track-wrap {
			overflow: hidden;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-track {
			display: grid;
			grid-template-columns: repeat(<?php echo esc_attr( count( $products ) ); ?>, calc(100% / var(--dotd-visible, 4)));
			transition: transform .45s cubic-bezier(.4,0,.2,1);
			will-change: transform;
		}

		/* ---------- Product card ---------- */
		#<?php echo esc_attr( $uid ); ?> .dotd-product-card {
			background: var(--white, #fff);
			border-radius: var(--radius-md, 10px);
			overflow: hidden;
			box-shadow: var(--shadow-card, 0 2px 8px rgba(0,0,0,.06));
			transition: all var(--transition, .25s cubic-bezier(.4,0,.2,1));
			position: relative;
			display: flex;
			flex-direction: column;
			margin: 0 8px;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-product-card:hover {
			box-shadow: var(--shadow-lg, 0 8px 30px rgba(0,0,0,.12));
			transform: translateY(-4px);
		}

		/* Image area */
		#<?php echo esc_attr( $uid ); ?> .dotd-card-img {
			position: relative;
			background: #F8F9FA;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 20px;
			overflow: hidden;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-card-img img {
			width: 100%;
			height: 200px;
			object-fit: contain;
			transition: transform .45s ease;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-product-card:hover .dotd-card-img img {
			transform: scale(1.07);
		}

		/* Hot badge */
		#<?php echo esc_attr( $uid ); ?> .dotd-hot-badge {
			position: absolute;
			top: 12px;
			right: 12px;
			background: #F59E0B;
			color: #fff;
			font-size: 11px;
			font-weight: 700;
			padding: 4px 12px;
			border-radius: 50px;
			z-index: 3;
			letter-spacing: .3px;
		}

		/* Sale badge */
		#<?php echo esc_attr( $uid ); ?> .dotd-sale-badge {
			position: absolute;
			top: 12px;
			left: 12px;
			background: var(--cta, #ea1d22);
			color: #fff;
			font-size: 11px;
			font-weight: 700;
			padding: 4px 10px;
			border-radius: 50px;
			z-index: 3;
		}

		/* Overlay actions */
		#<?php echo esc_attr( $uid ); ?> .dotd-overlay {
			position: absolute;
			top: 12px;
			right: -60px;
			display: flex;
			flex-direction: column;
			gap: 8px;
			opacity: 0;
			transition: all .3s cubic-bezier(.4,0,.2,1);
			pointer-events: none;
			z-index: 5;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-product-card:hover .dotd-overlay {
			right: 12px;
			opacity: 1;
			pointer-events: auto;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-overlay button {
			width: 36px;
			height: 36px;
			border-radius: 50%;
			background: #fff;
			border: none;
			box-shadow: 0 3px 10px rgba(0,0,0,.12);
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 14px;
			color: var(--text-dark, #121218);
			cursor: pointer;
			transition: all .2s;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-overlay button:hover {
			background: var(--cta, #ea1d22);
			color: #fff;
			box-shadow: 0 6px 18px rgba(234,29,34,.25);
		}

		/* Card body */
		#<?php echo esc_attr( $uid ); ?> .dotd-card-body {
			padding: 14px 16px 18px;
			display: flex;
			flex-direction: column;
			flex: 1;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-card-stars {
			display: flex;
			align-items: center;
			gap: 2px;
			margin-bottom: 6px;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-card-stars i {
			font-size: 12px;
			color: #F59E0B;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-card-stars .dotd-review-count {
			margin-left: 5px;
			font-size: 12px;
			color: var(--text-muted, #8E8EA0);
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-card-cat {
			font-size: 11px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: .7px;
			color: var(--text-muted, #8E8EA0);
			margin-bottom: 4px;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-card-name {
			font-size: 13px;
			font-weight: 600;
			color: var(--text-dark, #121218);
			line-height: 1.4;
			margin-bottom: 10px;
			display: -webkit-box;
			-webkit-line-clamp: 2;
			line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-card-name a {
			color: inherit;
			text-decoration: none;
			transition: color .2s;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-card-name a:hover {
			color: var(--primary, #3B3B98);
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-pricing {
			display: flex;
			align-items: center;
			gap: 8px;
			flex-wrap: wrap;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-price {
			font-size: 17px;
			font-weight: 700;
			color: var(--primary, #3B3B98);
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-old-price {
			font-size: 13px;
			color: var(--text-muted, #8E8EA0);
			text-decoration: line-through;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-price-range {
			font-size: 14px;
			font-weight: 700;
			color: var(--primary, #3B3B98);
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-atc-btn {
			margin-top: 12px;
			width: 100%;
			padding: 9px 10px;
			border-radius: 50px;
			border: 1.5px solid var(--border, #E0E2E8);
			background: transparent;
			color: var(--text-body, #555566);
			font-size: 13px;
			font-weight: 600;
			font-family: var(--font, 'Outfit', sans-serif);
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			cursor: pointer;
			transition: all var(--transition, .25s);
			text-decoration: none;
		}
		#<?php echo esc_attr( $uid ); ?> .dotd-atc-btn:hover {
			background: var(--primary, #3B3B98);
			color: #fff;
			border-color: var(--primary, #3B3B98);
		}

		/* ---------- Responsive ---------- */
		@media (max-width: 991px) {
			#<?php echo esc_attr( $uid ); ?> .dotd-track {
				grid-template-columns: repeat(<?php echo esc_attr( count( $products ) ); ?>, calc(100% / 2));
			}
			#<?php echo esc_attr( $uid ); ?> .dotd-inner {
				padding: 28px 44px;
			}
		}
		@media (max-width: 576px) {
			#<?php echo esc_attr( $uid ); ?> .dotd-track {
				grid-template-columns: repeat(<?php echo esc_attr( count( $products ) ); ?>, 100%);
			}
			#<?php echo esc_attr( $uid ); ?> .dotd-inner {
				padding: 24px 40px;
			}
			#<?php echo esc_attr( $uid ); ?> .dotd-pill {
				flex-wrap: wrap;
				justify-content: center;
				gap: 10px;
				padding: 12px 18px;
			}
		}
		</style>

		<!-- ── JS: countdown + carousel ── -->
		<script>
		(function(){
			var root = document.getElementById('<?php echo esc_js( $uid ); ?>');
			if (!root) return;

			/* ---- Countdown ---- */
			var cdWrap = root.querySelector('.dotd-countdown');
			var endTs  = cdWrap ? parseInt(cdWrap.dataset.end, 10) * 1000 : 0;

			function pad(n){ return n < 10 ? '0' + n : n; }

			function updateCd() {
				var diff = Math.max(0, endTs - Date.now());
				var d = Math.floor(diff / 86400000);
				var h = Math.floor((diff % 86400000) / 3600000);
				var m = Math.floor((diff % 3600000) / 60000);
				var s = Math.floor((diff % 60000) / 1000);
				var boxes = cdWrap ? cdWrap.querySelectorAll('.dotd-cd-box') : [];
				if (boxes[0]) boxes[0].textContent = pad(d)  + 'D';
				if (boxes[1]) boxes[1].textContent = pad(h)  + 'H';
				if (boxes[2]) boxes[2].textContent = pad(m)  + 'M';
				if (boxes[3]) boxes[3].textContent = pad(s)  + 'S';
			}
			if (endTs) { updateCd(); setInterval(updateCd, 1000); }

			/* ---- Carousel ---- */
			var track     = root.querySelector('.dotd-track');
			var prevBtn   = root.querySelector('.dotd-prev');
			var nextBtn   = root.querySelector('.dotd-next');
			if (!track || !prevBtn || !nextBtn) return;

			var cards     = track.querySelectorAll('.dotd-product-card');
			var total     = cards.length;
			var visible   = parseInt(getComputedStyle(track).getPropertyValue('--dotd-visible').trim(), 10) || 4;
			var current   = 0;

			function getVisible() {
				if (window.innerWidth <= 576)  return 1;
				if (window.innerWidth <= 991)  return 2;
				return parseInt(getComputedStyle(track).getPropertyValue('--dotd-visible').trim(), 10) || <?php echo esc_js( $visible ); ?>;
			}

			function go(idx) {
				var vis  = getVisible();
				var max  = Math.max(0, total - vis);
				current  = Math.max(0, Math.min(idx, max));
				var pct  = (100 / total) * current;
				track.style.transform = 'translateX(-' + pct + '%)';
				prevBtn.disabled = (current === 0);
				nextBtn.disabled = (current >= max);
				prevBtn.style.opacity = current === 0 ? '.4' : '1';
				nextBtn.style.opacity = current >= max ? '.4' : '1';
			}

			prevBtn.addEventListener('click', function(){ go(current - 1); });
			nextBtn.addEventListener('click', function(){ go(current + 1); });
			window.addEventListener('resize',  function(){ go(current); });
			go(0);
		})();
		</script>
		<?php
	}

	/* ============================================================
	   HELPER: render a single product card
	   ============================================================ */
	private function render_card( $product, $s ) {
		$id          = $product->get_id();
		$name        = $product->get_name();
		$permalink   = get_permalink( $id );
		$image       = get_the_post_thumbnail_url( $id, 'medium' ) ?: wc_placeholder_img_src( 'medium' );
		$on_sale     = $product->is_on_sale();
		$rating      = $product->get_average_rating();
		$review_cnt  = $product->get_review_count();
		$hot_label   = ! empty( $s['hot_badge_label'] ) ? $s['hot_badge_label'] : 'Hot';

		// Category
		$terms     = get_the_terms( $id, 'product_cat' );
		$cat_name  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

		// Sale %
		$sale_pct  = '';
		if ( $on_sale && $product->get_regular_price() && $product->get_sale_price() ) {
			$pct      = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
			$sale_pct = '-' . $pct . '%';
		}

		// Stars
		$full_s  = floor( $rating );
		$half_s  = ( $rating - $full_s >= 0.25 ) ? 1 : 0;
		$empty_s = 5 - $full_s - $half_s;

		// Price HTML — variable products show a range
		if ( $product->is_type( 'variable' ) ) {
			$min = wc_price( $product->get_variation_price( 'min' ) );
			$max = wc_price( $product->get_variation_price( 'max' ) );
			$price_html = $min === $max
				? '<span class="dotd-price">' . $min . '</span>'
				: '<span class="dotd-price-range">' . $min . ' – ' . $max . '</span>';
		} else {
			$sale_p   = $on_sale ? '<span class="dotd-old-price">' . wc_price( $product->get_regular_price() ) . '</span>' : '';
			$price_html = '<span class="dotd-price">' . wc_price( $product->get_price() ) . '</span>' . $sale_p;
		}

		// "Hot" = top rated or featured
		$is_hot   = ( $product->is_featured() || ( $rating >= 4.5 && $review_cnt > 0 ) );
		?>
		<div class="dotd-product-card product-card" data-product-id="<?php echo esc_attr( $id ); ?>">
			<div class="dotd-card-img pc-img">

				<?php if ( $sale_pct ) : ?>
					<span class="dotd-sale-badge"><?php echo esc_html( $sale_pct ); ?></span>
				<?php endif; ?>

				<?php if ( $is_hot ) : ?>
					<span class="dotd-hot-badge"><?php echo esc_html( $hot_label ); ?></span>
				<?php endif; ?>

				<a href="<?php echo esc_url( $permalink ); ?>">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ); ?>">
				</a>

				<!-- Overlay action buttons (wishlist / quickview / compare) -->
				<div class="dotd-overlay pc-overlay">
					<button title="Wishlist"><i class="fa-regular fa-heart"></i></button>
					<button title="Quick View"><i class="fa-regular fa-eye"></i></button>
					<button title="Compare"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
				</div>
			</div>

			<div class="dotd-card-body pc-body">
				<!-- Stars -->
				<div class="dotd-card-stars pc-stars">
					<?php
					for ( $i = 0; $i < $full_s;  $i++ ) echo '<i class="fa-solid fa-star"></i>';
					if ( $half_s )                       echo '<i class="fa-solid fa-star-half-stroke"></i>';
					for ( $i = 0; $i < $empty_s; $i++ ) echo '<i class="fa-regular fa-star"></i>';
					if ( $review_cnt > 0 ) echo '<span class="dotd-review-count">(' . esc_html( $review_cnt ) . ')</span>';
					?>
				</div>

				<?php if ( $cat_name ) : ?>
					<span class="dotd-card-cat pc-cat"><?php echo esc_html( $cat_name ); ?></span>
				<?php endif; ?>

				<h4 class="dotd-card-name">
					<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $name ); ?></a>
				</h4>

				<div class="dotd-pricing pc-pricing">
					<?php echo $price_html; ?>
				</div>

				<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
				   class="dotd-atc-btn atc-btn add_to_cart_button ajax_add_to_cart product_type_<?php echo esc_attr( $product->get_type() ); ?>"
				   data-product_id="<?php echo esc_attr( $id ); ?>"
				   data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
				   data-quantity="1"
				   aria-label="<?php echo esc_attr( 'Add ' . $name . ' to cart' ); ?>">
					<i class="fa-solid fa-cart-shopping"></i>
					<?php esc_html_e( 'Add to Cart', 'woocommerce' ); ?>
				</a>
			</div>
		</div>
		<?php
	}

	/* ============================================================
	   HELPER: fetch products
	   ============================================================ */
	private function get_products( $source, $limit ) {
		if ( ! function_exists( 'wc_get_products' ) ) return [];

		switch ( $source ) {
			case 'on_sale':
				$ids = wc_get_product_ids_on_sale();
				if ( ! empty( $ids ) ) {
					$q = new WP_Query( [
						'post_type'      => 'product',
						'posts_per_page' => $limit,
						'post__in'       => $ids,
						'orderby'        => 'rand',
						'post_status'    => 'publish',
					] );
					$prods = [];
					while ( $q->have_posts() ) {
						$q->the_post();
						$p = wc_get_product( get_the_ID() );
						if ( $p ) $prods[] = $p;
					}
					wp_reset_postdata();
					return $prods;
				}
				// Fall through to latest if no sale products
				return wc_get_products( [ 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish' ] );

			case 'featured':
				$prods = wc_get_products( [ 'limit' => $limit, 'featured' => true, 'status' => 'publish' ] );
				return ! empty( $prods ) ? $prods : wc_get_products( [ 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish' ] );

			case 'popular':
				$prods = wc_get_products( [ 'limit' => $limit, 'orderby' => 'popularity', 'order' => 'DESC', 'status' => 'publish' ] );
				return ! empty( $prods ) ? $prods : wc_get_products( [ 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish' ] );

			default: // latest
				return wc_get_products( [ 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish' ] );
		}
	}
}

/* ---- Self-register ---- */
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	$widgets_manager->register( new Zendotech_Deal_Of_The_Day_Widget() );
} );
