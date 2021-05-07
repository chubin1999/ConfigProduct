# ConfigProduct
Tạo sản phẩm config từ sản phẩm simple có trước
Điều kiện để tạo ra sản phẩm config như sau:
          + có 1 attr product code cho sản phẩm simple: b1

          + tất cả sản phẩm simple (b1) có giá trị product code giống nhau thì sẽ là sản phẩm con của sản phẩm config được tạo ra

          + SKU của sản phẩm config chính là product code của tất cả sản phẩm simple có giá trị đó giống nhau

          + phải có setting để chỉ định danh sách attr cho sản phẩm config (b2)

          + khi tất cả sản phẩm simple có product code giống nhau thì phải check các giá trị của b2 khác null thì mới cho ass sản phẩm simple vào config

           + phải có lớn hơn 0 sản phẩm simple thì mới cho tạo sản phẩm config

 

Yêu cầu: Đưa ra solution trước
