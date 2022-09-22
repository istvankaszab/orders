# Generated by Django 4.0.5 on 2022-06-26 18:32

from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        ('orders', '0022_remove_order_invoice'),
    ]

    operations = [
        migrations.RemoveField(
            model_name='orderitem',
            name='product',
        ),
        migrations.AddField(
            model_name='orderitem',
            name='brand',
            field=models.ForeignKey(default=1, on_delete=django.db.models.deletion.PROTECT, to='orders.brand'),
            preserve_default=False,
        ),
    ]
