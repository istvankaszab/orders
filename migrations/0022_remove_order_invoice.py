# Generated by Django 4.0.5 on 2022-06-26 17:26

from django.db import migrations


class Migration(migrations.Migration):

    dependencies = [
        ('orders', '0021_alter_order_invoice'),
    ]

    operations = [
        migrations.RemoveField(
            model_name='order',
            name='invoice',
        ),
    ]
