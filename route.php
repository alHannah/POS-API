<!-- Area:

http://127.0.0.1:8000/api/v1/store/area/create_update
POSTMAN:
id
name
brand_id

http://127.0.0.1:8000/api/v1/store/area/get
POSTMAN:
brandFilter[]

http://127.0.0.1:8000/api/v1/store/area/delete
POSTMAN:
id


Store Group:

http://127.0.0.1:8000/api/v1/store/store_group/create_update
POSTMAN:
id
group_name
brand_id

http://127.0.0.1:8000/api/v1/store/store_group/get
POSTMAN:
areaFilter[]
brandFilter[]

http://127.0.0.1:8000/api/v1/store/store_group/archive_activate
POSTMAN:
id

Drop Down:

http://127.0.0.1:8000/api/v1/store/drop_down/brand_dropdown
http://127.0.0.1:8000/api/v1/store/drop_down/store_group_dropdown
http://127.0.0.1:8000/api/v1/store/drop_down/price_tier_dropdown
http://127.0.0.1:8000/api/v1/store/drop_down/manager_dropdown
http://127.0.0.1:8000/api/v1/store/drop_down/add_product_dropdown
http://127.0.0.1:8000/api/v1/store/drop_down/stores_dropdown



Stores:

http://127.0.0.1:8000/api/v1/store/stores/create
http://127.0.0.1:8000/api/v1/store/stores/update
http://127.0.0.1:8000/api/v1/store/stores/delete
http://127.0.0.1:8000/api/v1/store/stores/get
http://127.0.0.1:8000/api/v1/store/stores/showProduct
http://127.0.0.1:8000/api/v1/store/stores/addProduct
http://127.0.0.1:8000/api/v1/store/stores/activateProduct


Schedule Group:

http://127.0.0.1:8000/api/v1/store/schedule_group/create
http://127.0.0.1:8000/api/v1/store/schedule_group/update
http://127.0.0.1:8000/api/v1/store/schedule_group/edit
http://127.0.0.1:8000/api/v1/store/schedule_group/delete
http://127.0.0.1:8000/api/v1/store/schedule_group/get


Store Hours:

http://127.0.0.1:8000/api/v1/store/schedule_group/create_update
http://127.0.0.1:8000/api/v1/store/schedule_group/delete
http://127.0.0.1:8000/api/v1/store/schedule_group/get -->

<!--
brand:
productCode:
productName:
productTag:
packaging:
uom:
min_uom:
product_classification:
category:
image: -->

<!--
product_code:
category:
product_classification:
pos_category:
uom:
min_uom:
product_tag:
imageFile:
brand:

Area:

http://127.0.0.1:8000/api/v1/store/area/create_update

id
name
brand_id

http://127.0.0.1:8000/api/v1/store/area/get

brandFilter[]

http://127.0.0.1:8000/api/v1/store/area/archive_activate

id


Store Group:

http://127.0.0.1:8000/api/v1/store/store_group/create_update

id
group_name
brand_id

http://127.0.0.1:8000/api/v1/store/store_group/get

areaFilter[]
brandFilter[]

http://127.0.0.1:8000/api/v1/store/store_group/delete

id


ung sa drop down:

Area:

http://127.0.0.1:8000/api/v1/store/drop_down/brand_dropdown

Store Group:

http://127.0.0.1:8000/api/v1/store/drop_down/brand_dropdown
http://127.0.0.1:8000/api/v1/store/drop_down/area_dropdown_get



Hello! Pede pagawan ulit mga api routes ung mga ginawa nyo with parameters (variables) and kung ano ung response na ibabato

Ex:
1. web/sample/routes
Parameters:
- store_id
- brand_id
Return:
- Table of Porducts where tagging is "s"



STORE SIDEBAR:
AREA:
CREATE OR UPDATE: /api/v1/store/area/create_update
Create Parameters:
name
brand_id
Update Parameters:
id
name
brand_id
UNDER CREATE OR UPDATE [BRAND DROPDOWN]: /api/v1/store/drop_down/brand_dropdown

GET: /api/v1/store/area/get
Get Parameter:
brandFilter[]

ARCHIVE OR ACTIVATE: /api/v1/store/area/archive_activate
Archive or Activate Parameter:
id

STORE GROUP:
CREATE OR UPDATE: /api/v1/store/store_group/create_update
Create Parameters:
group_name
brand_id
Update Parameters:
id
group_name
brand_id
UNDER CREATE OR UPDATE [BRAND DROPDOWN]: /api/v1/store/drop_down/brand_dropdown

GET: /api/v1/store/store_group/get
Get Parameter:
areaFilter[]
brandFilter[]

ARCHIVE OR ACTIVATE: /api/v1/store/store_group/delete
Archive or Activate Parameter:
id

PRODUCT SIDEBAR:
PRODUCT LIST:
CREATE OR UPDATE: /api/v1/product/product_list/create_update
UNDER CREATE OR UPDATE [BRAND DROPDOWN]:
/api/v1/product/drop_down/brand_dropdown
/api/v1/product/drop_down/uom_dropdown
/api/v1/product/drop_down/product_classification_dropdown
/api/v1/product/drop_down/category_dropdown
GET: /api/v1/product/product_list/get
ARCHIVE OR ACTIVATE: /api/v1/product/product_list/archive_activate

INVENTORY CATEGORY:
CREATE OR UPDATE: /api/v1/product/inventory_category/create_update
Create Parameter:
tag
name
Update Parameter:
id
tag
name

GET: /api/v1/product/inventory_category/get
Get Parameter:
status[]

ARCHIVE OR ACTIVATE: /api/v1/product/inventory_category/archive_activate
Archive or Activate Parameter:
id

ACCOUNTS SIDEBAR:
USER ACCESS:
CREATE: /api/v1/accounts/accounts/create
Create Parameters:
roleName
add[]
edit[]
view[]
delete[]
approve[] -->

