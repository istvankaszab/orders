# Generated by Django 4.0.5 on 2022-06-14 15:05

from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        ('orders', '0010_rename_customer_order_customer_id_and_more'),
    ]

    operations = [
        migrations.AlterField(
            model_name='order',
            name='original_id',
            field=models.CharField(editable=False, max_length=15),
        ),
        migrations.AlterField(
            model_name='order',
            name='webshop_id',
            field=models.ForeignKey(on_delete=django.db.models.deletion.PROTECT, to='orders.webshop', verbose_name='Webshop'),
        ),
    ]
