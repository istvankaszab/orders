from django.contrib import admin

from .models import Customer, Note, Webshop, Order, Status, Update, Supplier, Brand, Purchase, OrderItem


admin.site.register(Customer)
admin.site.register(Note)
admin.site.register(Webshop)
admin.site.register(Order)
admin.site.register(Status)
admin.site.register(Update)
admin.site.register(Supplier)
admin.site.register(Brand)
admin.site.register(Purchase)
admin.site.register(OrderItem)
