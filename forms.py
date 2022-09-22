from django import forms
from django.utils.translation import gettext_lazy as _

from .models import Order, Customer, Supplier, Brand, Purchase, OrderItem, Note

class OrderForm(forms.ModelForm):
  class Meta:
    model = Order
    fields = "__all__"

class CustomerForm(forms.ModelForm):
  class Meta:
    model = Customer
    fields = ('name', 'email', 'phone', 'address', 'city', 'postcode')
    labels = {
      'name': _('Customer name'),
    }
    widgets = {
      'name': forms.TextInput(attrs={'class': 'form-control'}),
      'email': forms.EmailInput(attrs={'class': 'form-control'}),
      'phone': forms.TextInput(attrs={'class': 'form-control'}),
      'city': forms.TextInput(attrs={'class': 'form-control'}),
      'address': forms.TextInput(attrs={'class': 'form-control'}),
      'postcode': forms.TextInput(attrs={'class': 'form-control'}),
    }


class SupplierForm(forms.ModelForm):
  class Meta:
    model = Supplier
    fields = ('name',)
    widgets = {
      'name': forms.TextInput(attrs={'class': 'form-control'})
    }


class BrandForm(forms.ModelForm):
  class Meta:
    model = Brand
    fields = ('name', 'supplier')
    widgets = {
      'name': forms.TextInput(attrs={'class': 'form-control'}),
      'supplier': forms.Select(attrs={'class': 'form-control'}),
    }


class PurchaseForm(forms.ModelForm):
  class Meta:
    model = Purchase()
    fields = ('supplier',)
    widgets = {
      'supplier': forms.Select(attrs={'class': 'form-control'}),
    }


class OrderItemForm(forms.ModelForm):
  class Meta:
    model = OrderItem
    fields = ('order', 'name', 'price', 'brand', 'status', 'quantity')
    widgets = {
      'order': forms.HiddenInput(),
      'name': forms.TextInput(attrs={'class': 'form-control'}),
      'price': forms.NumberInput(attrs={'class': 'form-control'}),
      'brand': forms.Select(attrs={'class': 'form-control'}),
      'status': forms.Select(attrs={'class': 'form-control'}),
      'quantity': forms.NumberInput(attrs={'class': 'form-control'})
    }


class NoteForm(forms.ModelForm):
  class Meta:
    model = Note
    fields = ('order', 'text', 'courier')
    widgets = {
      'order': forms.HiddenInput(),
      'text': forms.Textarea(attrs={'class': 'form-control', 'rows': '5'}),
    }

