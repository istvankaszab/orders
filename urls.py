from django.urls import path

from . import views

app_name = 'orders'
urlpatterns = [
  path('', views.IndexView.as_view(), name='index'),
  path('new', views.CustomerCreateView.as_view(), name='new'),  
  path('<int:pk>/', views.OrderDetailView.as_view(), name='order'),
  path('customer/<int:pk>', views.CustomerUpdateView.as_view(), name='customer-update'),
  path('<order_id>/item/new', views.OrderItemCreateView.as_view(), name='orderitem-create'),
  path('item/<int:pk>', views.OrderItemUpdateView.as_view(), name='orderitem-update'),
  path('item/<int:pk>/delete', views.OrderItemDeleteView.as_view(), name='orderitem-delete'),
  path('<order_id>/note/new', views.NoteCreateView.as_view(), name='note-create'),
  path('note/<int:pk>', views.NoteUpdateView.as_view(), name='note-update'),
  path('note/<int:pk>/delete', views.NoteDeleteView.as_view(), name='note-delete'),
  path('suppliers', views.SuppliersView.as_view(), name='suppliers'),
  path('supplier/new', views.SupplierCreateView.as_view(), name='supplier-new'),
  path('supplier/<int:pk>', views.SupplierUpdateView.as_view(), name='supplier-update'),
  path('supplier/<int:pk>/delete', views.SupplierDeleteView.as_view(), name='supplier-delete'),
  path('brands', views.BrandsView.as_view(), name='brands'),
  path('brand/new', views.BrandCreateView.as_view(), name='brand-new'),
  path('brand/<int:pk>', views.BrandUpdateView.as_view(), name='brand-update'),
  path('purchases', views.PurchasesView.as_view(), name='purchases'),
]
