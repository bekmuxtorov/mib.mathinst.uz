# Generated by Django 4.1.6 on 2023-02-25 12:57

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('mainapp', '0009_article_ordinal_number_alter_article_status'),
    ]

    operations = [
        migrations.RemoveField(
            model_name='issue',
            name='name_en',
        ),
        migrations.RemoveField(
            model_name='issue',
            name='name_uz',
        ),
        migrations.AddField(
            model_name='issue',
            name='ordinal_number',
            field=models.IntegerField(default=0, verbose_name='Jurnal tartib raqami'),
        ),
    ]