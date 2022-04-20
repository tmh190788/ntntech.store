<?php
/**
 * Cấu hình cơ bản cho WordPress
 *
 * Trong quá trình cài đặt, file "wp-config.php" sẽ được tạo dựa trên nội dung
 * mẫu của file này. Bạn không bắt buộc phải sử dụng giao diện web để cài đặt,
 * chỉ cần lưu file này lại với tên "wp-config.php" và điền các thông tin cần thiết.
 *
 * File này chứa các thiết lập sau:
 *
 * * Thiết lập MySQL
 * * Các khóa bí mật
 * * Tiền tố cho các bảng database
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Thiết lập MySQL - Bạn có thể lấy các thông tin này từ host/server ** //
/** Tên database MySQL */
define( 'DB_NAME', 'ntntech_store' );

/** Username của database */
define( 'DB_USER', 'root' );

/** Mật khẩu của database */
define( 'DB_PASSWORD', '' );

/** Hostname của database */
define( 'DB_HOST', 'localhost' );

/** Database charset sử dụng để tạo bảng database. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Kiểu database collate. Đừng thay đổi nếu không hiểu rõ. */
define('DB_COLLATE', '');

/**#@+
 * Khóa xác thực và salt.
 *
 * Thay đổi các giá trị dưới đây thành các khóa không trùng nhau!
 * Bạn có thể tạo ra các khóa này bằng công cụ
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Bạn có thể thay đổi chúng bất cứ lúc nào để vô hiệu hóa tất cả
 * các cookie hiện có. Điều này sẽ buộc tất cả người dùng phải đăng nhập lại.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '``17JmL]^7tf/=n!oVfDfAZRr5wCr,`@$rL07M/F#0=E<q/r$:PGVI#b[#yfJLj%' );
define( 'SECURE_AUTH_KEY',  '`;m-5FGv2Jp@BR?KmHI8T88*?j]Ra)X)#|g,%WqKQy%y} BU#f3hbwP19Q< BF]:' );
define( 'LOGGED_IN_KEY',    'f[Qm:4RcYXj|GlxkF0| K|$lBs}LBk=j-6l^/|Nh?jH#O3c/%~n=O|tupK4zBZuK' );
define( 'NONCE_KEY',        '_l`p7(d0r:&7hKe^+!4;v.O5ETXM9cZ4>D]^!g{w+Ohyj<xK%:2S~0Us01ppB<PZ' );
define( 'AUTH_SALT',        'FH{n0bWI%&s^ZjtB{l|@{61N,O|=?Nq`d3%7}EG<eV@k9pHGWOdU=a<5SVTO)HxB' );
define( 'SECURE_AUTH_SALT', 'TC^=DJ$uQxPXx| #_+t68Ft SD(x9}(JS*.[jlT4FXcSIvf[PghEkF4ob/0$Ao~-' );
define( 'LOGGED_IN_SALT',   'Fi`XlNt1&t7Z0k]UCTE?}ns7,x9)* 1<xig@H>Wbm|&?Pqi?kNahodUN7ngVBy6^' );
define( 'NONCE_SALT',       ' 6rmLC2uO`Y}O(>efM:KpK?2At-,vnH$i7>N=8jjaj,vUADK2&1S!3rhw$Cy+<m?' );

/**#@-*/

/**
 * Tiền tố cho bảng database.
 *
 * Đặt tiền tố cho bảng giúp bạn có thể cài nhiều site WordPress vào cùng một database.
 * Chỉ sử dụng số, ký tự và dấu gạch dưới!
 */
$table_prefix = 'ntn_';

/**
 * Dành cho developer: Chế độ debug.
 *
 * Thay đổi hằng số này thành true sẽ làm hiện lên các thông báo trong quá trình phát triển.
 * Chúng tôi khuyến cáo các developer sử dụng WP_DEBUG trong quá trình phát triển plugin và theme.
 *
 * Để có thông tin về các hằng số khác có thể sử dụng khi debug, hãy xem tại Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Đó là tất cả thiết lập, ngưng sửa từ phần này trở xuống. Chúc bạn viết blog vui vẻ. */

/** Đường dẫn tuyệt đối đến thư mục cài đặt WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Thiết lập biến và include file. */
require_once(ABSPATH . 'wp-settings.php');