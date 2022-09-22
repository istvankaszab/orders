from django.db import models
from django.db.models.signals import post_save, post_delete

class Customer(models.Model):
  name = models.CharField(max_length=50)
  email = models.CharField(max_length=50)
  phone = models.CharField(max_length=15)
  city = models.CharField(max_length=50)
  address = models.CharField(max_length=100)
  postcode = models.CharField(max_length=8)

  def __str__(self):
    return self.name


class Webshop(models.Model):
  name = models.CharField(max_length=30)
  uri = models.CharField(max_length=40)

  def __str__(self):
    return self.name


class Order(models.Model):
  customer = models.ForeignKey(Customer, on_delete=models.PROTECT)
  webshop = models.ForeignKey(Webshop, on_delete=models.PROTECT, verbose_name='Webshop')
  original = models.CharField(max_length=15, blank=True)

  def total(self):
    items = OrderItem.objects.filter(order=self.id)
    sum = 0
    for item in items:
      sum += item.price * item.quantity
    return sum

  def get_suppliers(self):
    items = OrderItem.objects.filter(order=self.id)
    partners = []

    for item in items:
      if item.brand.supplier.name not in partners:
        partners.append(item.brand.supplier.name)
    return partners

  def set_status(self):
    items = OrderItem.objects.filter(order=self.pk)
    status = 'ordered'
    statuses = []
    for item in items:
      statuses.append(item.status.name)
    if 'purchased' in statuses:
      status = 'purchased'
    if 'purchasing' in statuses or 'running late' in statuses or 'not available' in statuses:
      status = 'purchasing'
    if 'ordered' in statuses:
      status = 'ordered'
    status_id = Status.objects.get(name=status)
    if status_id != Update.objects.filter(order=self.id).order_by('-time')[0].status:
      Update.objects.create(order=self, status=status_id)
    

  def get_status(self):
    status = Update.objects.filter(order=self.id).order_by('-time')[0].status.name
    return status

  def items(self):
    items = OrderItem.objects.filter(order=self.id)
    return items


class Status(models.Model):
  name = models.CharField(max_length=15)
  style_class = models.TextField()
  
  def __str__(self):
    return self.name

class Update(models.Model):
  order = models.ForeignKey(Order, on_delete=models.CASCADE)
  status = models.ForeignKey(Status, on_delete=models.PROTECT)
  time = models.DateTimeField(auto_now_add=True)


class Note(models.Model):
  order = models.ForeignKey(Order, on_delete=models.CASCADE)
  text = models.TextField()
  courier = models.BooleanField(default=False)


class Supplier(models.Model):
  name = models.CharField(max_length=20, unique=True)

  def __str__(self):
    return self.name

  def get_brands(self):
    brands = Brand.objects.filter(supplier=self.id)
    return brands

class Brand(models.Model):
  name = models.CharField(max_length=20)
  supplier = models.ForeignKey(Supplier, on_delete=models.PROTECT)

  def __str__(self):
    return self.name


class Purchase(models.Model):
  supplier = models.ForeignKey(Supplier, on_delete=models.PROTECT)
  status = models.ForeignKey(Status, on_delete=models.PROTECT)


class OrderItem(models.Model):
  order = models.ForeignKey(Order, on_delete=models.CASCADE)
  name = models.CharField(max_length=100)
  price = models.DecimalField(max_digits=12, decimal_places=2)
  brand = models.ForeignKey(Brand, on_delete=models.PROTECT)
  purchase = models.ForeignKey(Purchase, blank=True, null=True, on_delete=models.PROTECT)
  status = models.ForeignKey(Status, on_delete=models.PROTECT)
  quantity = models.IntegerField()

  def __str__(self):
    return self.name


class PurchaseItem(models.Model):
  orderitem = models.ForeignKey(OrderItem, on_delete=models.CASCADE)
  purchase = models.ForeignKey(Purchase, on_delete=models.CASCADE)


### signals ###

def orderitem_post_save(*args, **kwargs):
  kwargs['instance'].order.set_status()

post_save.connect(orderitem_post_save, sender=OrderItem)

def orderitem_post_delete(*args, **kwargs):
  kwargs['instance'].order.set_status()

post_delete.connect(orderitem_post_delete, sender=OrderItem)
