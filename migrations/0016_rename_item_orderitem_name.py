# Generated by Django 4.0.5 on 2022-06-20 14:26

from django.db import migrations


class Migration(migrations.Migration):

    dependencies = [
        ('orders', '0015_orderitem_item_alter_orderitem_product'),
    ]

    operations = [
        migrations.RenameField(
            model_name='orderitem',
            old_name='item',
            new_name='name',
        ),
    ]