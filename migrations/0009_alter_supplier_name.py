# Generated by Django 4.0.4 on 2022-06-12 20:13

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('orders', '0008_rename_supplier_brand_supplier_id'),
    ]

    operations = [
        migrations.AlterField(
            model_name='supplier',
            name='name',
            field=models.CharField(max_length=20, unique=True),
        ),
    ]
