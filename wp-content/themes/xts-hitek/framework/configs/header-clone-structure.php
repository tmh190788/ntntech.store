<?php
/**
 * Header clone structure template
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$template = '
    <div class="xts-header-clone xts-header-inner">
        <div class="<%cloneClass%>">
            <div class="container">
                <div class="xts-header-row-inner">
                    <div class="xts-header-col xts-start xts-desktop">
                        <%.xts-logo%>
                    </div>
                    <div class="xts-header-col xts-center xts-desktop">
                        <%.xts-nav-main%>
                    </div>
                    <div class="xts-header-col xts-end xts-desktop">
                        <%.xts-header-search%>
                        <%.xts-header-my-account%>
                        <%.xts-header-wishlist%>
                        <%.xts-header-compare%>
                        <%.xts-header-cart%>
                    </div>
                    <%.xts-start.xts-mobile%>
                    <%.xts-center.xts-mobile%>
                    <%.xts-end.xts-mobile%>
                </div>
            </div>
        </div>
    </div>
';

return apply_filters( 'xts_header_clone_template', $template );
