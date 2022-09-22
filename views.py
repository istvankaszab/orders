from django.shortcuts import redirect
from django.views import generic
from django.urls import reverse_lazy

from .forms import OrderForm, CustomerForm, SupplierForm
from .forms import OrderItemForm, NoteForm, BrandForm, PurchaseForm

from .models import Order, OrderItem, Supplier, Brand, Customer
from .models import Note, Webshop, Status, Update, Purchase


class IndexView(generic.ListView):
  template_name = 'orders/index.html'
  context_object_name = 'orders'
  paginate_by = 20
  filterset_class = ''

  def get_context_data(self, **kwargs):
    context = super().get_context_data(**kwargs)
    context['suppliers'] = Supplier.objects.all()
    supplier = self.request.GET.get('supplier', '')
    if supplier:
      context['selected_supplier'] = supplier
    context['order_status'] = Update.objects.filter(order=1)
    return context

  def get_queryset(self):
    supplier = self.request.GET.get('supplier', '')
    if supplier:
      brands = Brand.objects.filter(supplier=supplier).values_list('id', flat=True)
      items = OrderItem.objects.filter(brand__in=brands).values_list('order', flat=True)
      orders = Order.objects.filter(pk__in=items)
    else:
      orders = Order.objects.all()

    return orders


class OrderDetailView(generic.DetailView):
  template_name = 'orders/order.html'
  model = Order

  def get_context_data(self, **kwargs):
    context = super().get_context_data(**kwargs)
    context['customer'] = Customer.objects.get(pk=self.object.customer.id)
    context['items'] = OrderItem.objects.filter(order=self.object.pk)
    context['notes'] = Note.objects.filter(order=self.object.pk)
    context['updates'] = Update.objects.filter(order=self.object.pk)
    return context


class CustomerCreateView(generic.CreateView):
  model = Customer
  form_class = CustomerForm
  template_name = 'orders/customer.html'
  success_url = '/orders'
  
  def get_context_data(self, **kwargs):
    context = super().get_context_data(**kwargs)
    context['order_id'] = 0
    return context
  
  def form_valid(self, form):
    self.object = form.save()
    webshop = Webshop.objects.get(name='store')
    order = Order(customer_id=self.object.id, webshop_id=webshop.id)
    order.save()
    status = Status.objects.get(name='ordered')
    update = Update(order=order.id, status=status)
    update.save()
    return redirect('orders:order', pk=order.id)

class CustomerUpdateView(generic.UpdateView):
  model = Customer
  form_class = CustomerForm
  template_name = 'orders/customer.html'

  def get_success_url(self):
    order = Order.objects.get(customer=self.object.id)
    return reverse_lazy('orders:order', kwargs={'pk':order.id})

  def get_context_data(self, **kwargs):
    context = super().get_context_data(**kwargs)
    order = Order.objects.get(customer=self.object.id)
    context['order_id'] = order.id
    return context


class SuppliersView(generic.ListView):
  template_name = 'orders/suppliers.html'
  context_object_name = 'suppliers'

  def get_queryset(self):
    return Supplier.objects.order_by('name').all()

class SupplierCreateView(generic.CreateView):
  model = Supplier
  form_class = SupplierForm
  template_name = 'orders/supplier.html'
  success_url = '/orders/suppliers'

class SupplierUpdateView(generic.UpdateView):
  model = Supplier
  form_class = SupplierForm
  template_name = 'orders/supplier.html'
  success_url = '/orders/suppliers'


class SupplierDeleteView(generic.DeleteView):
  model = Supplier
  success_url = '/orders/suppliers'


class BrandsView(generic.ListView):
  template_name = 'orders/brands.html'
  context_object_name = 'brands'
  paginate_by = 10

  def get_queryset(self):
    return Brand.objects.order_by('name').all()


class BrandCreateView(generic.CreateView):
  model = Brand
  form_class = BrandForm
  template_name = 'orders/brand.html'
  success_url = '/orders/brands'

class BrandUpdateView(generic.UpdateView):
  model = Brand
  form_class = BrandForm
  template_name = 'orders/brand.html'
  success_url = '/orders/brands'


class PurchasesView(generic.ListView):
  template_name = 'orders/purchases.html'
  context_object_name = 'purchases'
  paginate_by = 10

  def get_queryset(self):
    return Purchase.objects.order_by('id').all()


class OrderItemCreateView(generic.CreateView):
  model = OrderItem
  form_class = OrderItemForm
  template_name = 'orders/orderitem.html'

  def get_success_url(self):
    order = Order.objects.get(pk=self.object.order.id)
    return reverse_lazy('orders:order', kwargs={'pk':order.id})

  def get_context_data(self, **kwargs):
    context = super().get_context_data(**kwargs)
    context['order_id'] = self.kwargs['order_id']

    return context


class OrderItemUpdateView(generic.UpdateView):
  model = OrderItem
  form_class = OrderItemForm
  template_name = 'orders/orderitem.html'

  def get_success_url(self):
    order = Order.objects.get(pk=self.object.order.id)
    return reverse_lazy('orders:order', kwargs={'pk':order.id})

  def get_context_data(self, **kwargs):
    context = super().get_context_data(**kwargs)
    order = Order.objects.get(pk=self.object.order.id)
    context['order_id'] = order.id
    return context


class OrderItemDeleteView(generic.DeleteView):
  model = OrderItem

  def get_success_url(self):
    order = Order.objects.get(pk=self.object.order.id)
    return reverse_lazy('orders:order', kwargs={'pk':order.id})


class NoteCreateView(generic.CreateView):
  model = Note
  form_class = NoteForm
  template_name = 'orders/note.html'

  def get_success_url(self):
    order = Order.objects.get(pk=self.object.order.id)
    return reverse_lazy('orders:order', kwargs={'pk':order.id})

  def get_context_data(self, **kwargs):
    context = super().get_context_data(**kwargs)
    context['order_id'] = self.kwargs['order_id']
    return context


class NoteUpdateView(generic.UpdateView):
  model = Note
  form_class = NoteForm
  template_name = 'orders/note.html'

  def get_success_url(self):
    order = Order.objects.get(pk=self.object.order.id)
    return reverse_lazy('orders:order', kwargs={'pk':order.id})

  def get_context_data(self, **kwargs):
    context = super().get_context_data(**kwargs)
    order = Order.objects.get(pk=self.object.order.id)
    context['order_id'] = order.id
    return context


class NoteDeleteView(generic.DeleteView):
  model = Note

  def get_success_url(self):
    order = Order.objects.get(pk=self.object.order.id)
    return reverse_lazy('orders:order', kwargs={'pk':order.id})